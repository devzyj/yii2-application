<?php
/**
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 */
namespace backup\behaviors;

use Yii;
use yii\web\Response;

/**
 * FormatterBehavior 是在 [[\yii\web\Response::EVENT_BEFORE_SEND]] 中格式化 RESTful API 最终响应结果的行为类。
 * 
 * 响应中的错误编码使用的是 `Yii::t($errorCodeCategory, $message)` 翻译功能。
 * 使用错误编码，需要设置 [[$errorCodeCategory]]。
 * 使用方法如下：
 * 
 * ```php
 * 'components' => [
 *     'response' => [
 *         // 格式化响应结果的行为。
 *         'as formatterBehavior' => [
 *             'class' => 'library\rest\FormatterBehavior',
 *             'errorCodeCategory' => 'error/code',
 *         ]
 *     ],
 *     'i18n' => [
 *         'translations' => [
 *             // 通过错误信息翻译错误编码的配置。
 *             'error/code*' => [
 *                 'class' => 'yii\i18n\PhpMessageSource',
 *                 'forceTranslation' => true,
 *                 'sourceLanguage' => 'en-US',
 *                 'fileMap' => [
 *                     'error/code' => 'error.code.php',
 *                     'error/code/422' => 'error.code.422.php',
 *                     'error/code/500' => 'error.code.500.php',
 *                 ],
 *                 'on missingTranslation' => function ($event) {
 *                     // 未知的错误编码。
 *                     $event->translatedMessage = 0;
 *                 }
 *             ]
 *         ]
 *     ]
 * ]
 * ```
 * 
 * 响应结果说明如下：
 * ```php
 * // 调用更新接口。
 * PUT /users/10
 *  {
 *      "username": "test",
 *      "password": "123456",
 *      ...
 *  }
 * 
 * // 成功的返回值由接口决定。
 * {
 *     "user_id": "10",
 *     "username": "test",
 *     ...
 * }
 * 
 * // 失败的基本返回值。
 * {
 *     "status": 404, // HTTP状态码。
 *     "message": "Object not found.", // 错误描述。
 *     "code": 4221, // 错误编码。
 * }
 * 
 * // 详细的失败返回值。
 * {
 *     "status": 422, // HTTP状态码。
 *     "message": "Data Validation Failed.", // 错误描述。
 *     "code": 4221, // 错误编码。
 *     "errors": [
 *         {
 *             "field": "username", // 错误属性名。
 *             "message": "username error.", // 错误描述。
 *             "code": 4222 // 错误编码。
 *         },
 *         {
 *             "field": "password", // 错误属性名。
 *             "message": "password error.", // 错误描述。
 *             "code": 4223 // 错误编码。
 *         },
 *         ...
 *     ]
 * }
 * 
 * // 在程序中判断返回结果。
 * if ($httpCode >= 200 && $httpCode < 300) {
 *     // 成功。
 * } elseif ($httpCode >= 400 && $httpCode < 600) {
 *     // 错误。
 * } else {
 *     // 其它状态处理。
 * }
 * ```
 * 
 * HTTP 状态代码说明如下：
 * 200: OK。一切正常。
 * 201: 响应 POST 请求时成功创建一个资源。Location header 包含的URL指向新创建的资源。
 * 204: 该请求被成功处理，响应不包含正文内容 (类似 DELETE 请求)。
 * 304: 资源没有被修改。可以使用缓存的版本。
 * 400: 错误的请求。可能通过用户方面的多种原因引起的，例如在请求体内有无效的JSON 数据，无效的操作参数，等等。
 * 401: 身份验证失败。
 * 403: 已经经过身份验证的用户不允许访问指定的 API 末端。
 * 404: 所请求的资源不存在。
 * 405: 不被允许的方法。 请检查 Allow header 允许的HTTP方法。
 * 415: 不支持的媒体类型。 所请求的内容类型或版本号是无效的。
 * 422: 数据验证失败 (例如，响应一个 POST 请求)。 请检查响应体内详细的错误消息。
 * 429: 请求过多。 由于限速请求被拒绝。
 * 500: 内部服务器错误。 这可能是由于内部程序错误引起的。
 * 
 * 注意：该行为需要附加到 [[Response]] 中才能正常使用。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 * @deprecated
 */
class FormatterBehavior extends \yii\base\Behavior
{
    /**
     * @var string 翻译错误编码时的类型。
     */
    public $errorCodeCategory;

    /**
     * @var string 错误编码类型加上 HTTP 状态码的模板。
     * @see getErrorCode()
     */
    public $fileMapCategoryTemplate = '{category}/{statusCode}';
    
    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            Response::EVENT_BEFORE_SEND => 'beforeSend',
        ];
    }
    
    /**
     * 处理需要显示的响应数据。
     * 
     * @param \yii\base\Event $event
     * @see \yii\web\Response::send()
     */
    public function beforeSend($event)
    {
        /* @var $response \yii\web\Response */
        $response = $event->sender;
        
        // `$data` 不是数组时，不进行处理。
        if (!is_array($response->data)) {
            return;
        }
        
        // 处理响应结果。
        if ($response->getIsSuccessful()) {
            // statusCode >= 200 && statusCode < 300
            $data = $this->processSuccessful($response);
        } elseif ($response->getIsClientError()) {
            // statusCode >= 400 && statusCode < 500
            $data = $this->processClientError($response);
        } elseif ($response->getIsServerError()) {
            // statusCode >= 500 && statusCode < 600
            $data = $this->processServerError($response);
        } else {
            $data = $this->processOther($response);
        }
        
        // 在调试模式下显示更多的错误信息。
        if(YII_DEBUG && ($response->getIsClientError() || $response->getIsServerError())) {
            $data['rawData'] = $response->data;
        }
        
        // 重新设置响应数据。
        $response->data = $data;
    }

    /**
     * 处理成功的响应数据。statusCode >= 200 && statusCode < 300
     *
     * @param \yii\web\Response $response 响应对像。
     * @return array 处理后的数据。
     */
    protected function processSuccessful($response)
    {
        // 成功的数据。
        $result = $response->data;
        
        // 处理批量动作返回的数据。（批量动作始终返回 200 状态）
        if ($this->isBatchResult($result)) {
            // 调试日志。
            Yii::debug('Process the result for batch action', __METHOD__);
            
            // 循环处理批量动作的结果。
            foreach ($result as $key => $value) {
                if ($value['success'] === false) {
                    $result[$key]['data'] = $this->processUnprocessableEntity($value['data']);
                }
            }
        }
        
        return $result;
    }

    /**
     * 处理客户端错误的响应数据。statusCode >= 400 && statusCode < 500
     *
     * @param \yii\web\Response $response 响应对像。
     * @return array|null 处理后的数据。
     */
    protected function processClientError($response)
    {
        // 基础的错误信息。
        $result = $this->processError($response);
    
        // 验证数据失败的异常，需要处理具体的错误信息。
        if ($response->statusCode === 422) {
            $result['errors'] = $this->processUnprocessableEntity($response->data);
        }
    
        // 返回结果。
        return $result;
    }
    
    /**
     * 处理服务器端错误的响应数据。statusCode >= 500 && statusCode < 600
     *
     * @param \yii\web\Response $response 响应对像。
     * @return array|null 处理后的数据。
     */
    protected function processServerError($response)
    {
        return $this->processError($response);
    }
    
    /**
     * 处理其它状态的响应数据。
     *
     * @param \yii\web\Response $response 响应对像。
     * @return array 处理后的数据。
     */
    protected function processOther($response)
    {
        return $response->data;
    }

    /**
     * 处理错误的响应数据。
     *
     * @param \yii\web\Response $response 响应对像。
     * @return array 处理后的数据。
     */
    protected function processError($response)
    {
        // 错误状态。
        $status = $response->statusCode;
        
        // 错误描述。
        $message = isset($response->data['message']) ? $response->data['message'] : $response->statusText;
        
        // 错误编码。
        if ($this->errorCodeCategory !== null) {
            // 通过错误信息查找错误编码。
            $code = $this->getErrorCode($message, $status);
        } else {
            $code = isset($response->data['code']) ? $response->data['code'] : 0;
        }
        
        // 构造并且返回错误结果。
        return [
            'status' => $status,
            'message' => $message,
            'code' => $code
        ];
    }
    
    /**
     * 处理 HTTP 422 异常时的错误内容。
     * 
     * @param array $errors
     * @return array
     */
    protected function processUnprocessableEntity($errors)
    {
        // 添加错误编码。
        if ($this->errorCodeCategory !== null && is_array($errors) && is_array(reset($errors))) {
            foreach ($errors as $key => $error) {
                if (count($error) === 2 && isset($error['field']) && isset($error['message'])) {
                    $errors[$key]['code'] = $this->getErrorCode($error['message'], 422);
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * 判断是否为批量动作返回的数据。
     * 
     * @param mixed $data
     * @return boolean
     */
    protected function isBatchResult($data)
    {
        if (is_array($data) && is_array(reset($data))) {
            $row = current($data);
            if (count($row) === 2 && isset($row['success']) && isset($row['data'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 通过错误描述，获取错误编码。
     * 
     * @param string $message
     * @param integer $statusCode
     * @return integer|null
     */
    protected function getErrorCode($message, $statusCode)
    {
        if ($this->errorCodeCategory !== null) {
            $category = $this->errorCodeCategory;
            $messageSource = Yii::$app->getI18n()->getMessageSource($category);
            if ($messageSource instanceof \yii\i18n\MessageSource) {
                $fileMap = $messageSource->fileMap;
                $fileMapCategory = strtr($this->fileMapCategoryTemplate, ['{category}' => $category, '{statusCode}' => $statusCode]);
                if (isset($fileMap[$fileMapCategory])) {
                    return Yii::t($fileMapCategory, $message);
                }
            }

            return Yii::t($category, $message);
        }
    }
}