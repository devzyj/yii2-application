<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components\filters;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * TimestampRangeLimiter 实现了判断请求参数中时间戳参数的有效范围。
 * 
 * 当时间戳参数超出范围时，会抛出一个 [[\yii\web\BadRequestHttpException]] 异常。
 * 
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'timestampRangeLimiter' => [
 *             'class' => 'api\components\filters\TimestampRangeLimiter',
 *         ],
 *     ];
 * }
 * ```
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 * @deprecated 暂不使用。
 */
class TimestampRangeLimiter extends \yii\base\ActionFilter
{
    /**
     * @var bool 是否在响应中包含时间戳范围限制标头。
     */
    public $enableResponseHeaders = true;
    
    /**
     * @var string|callable 传递时间戳的参数名，或者是一个返回时间戳的回调函数。
     * 
     * ```php
     * function ($request, $action) {
     *     // $request 当前请求对像。
     *     // $action 将要执行的动作。
     * }
     * ```
     */
    public $paramName = 'tp';
    
    /**
     * @var array 时间范围。默认为前后 300 秒。
     */
    public $range = [300, 300];

    /**
     * @var boolean 时间戳为空时是否跳过检查。
     */
    public $skipOnEmpty = false;
    
    /**
     * @var callable 判断时间戳是否为空的回调函数。函数应该返回一个 `boolean` 值。
     * 
     * ```php
     * function ($value) {
     *     // $value 需要判断的时间戳。
     * }
     * ```
     */
    public $isEmpty;
    
    /**
     * @var string 当时间戳超出范围时显示的消息。
     */
    public $errorMessage;
    
    /**
     * @var \yii\web\Request 当前的请求。如果没有设置，将使用 `Yii::$app->getRequest()`。
     */
    public $request;
    
    /**
     * @var \yii\web\Response 要发送的响应。如果没有设置，将使用 `Yii::$app->getResponse()`。
     */
    public $response;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->errorMessage === null) {
            $this->errorMessage = 'The request has expired.';
        }
        
        if ($this->request === null) {
            $this->request = Yii::$app->getRequest();
        }
        
        if ($this->response === null) {
            $this->response = Yii::$app->getResponse();
        }
    }
    
    /**
     * {@inheritdoc}
     * 
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (is_string($this->paramName)) {
            $timestamp = (int) $this->request->getQueryParam($this->paramName);
        } else {
            $timestamp = (int) call_user_func($this->paramName, $this->request, $action);
        }
        
        if ($this->skipOnEmpty && $this->isEmpty($timestamp)) {
            Yii::debug('Skipped: timestamp is empty.', __METHOD__);
            return true;
        }
        
        Yii::debug('Check timestamp range limit.', __METHOD__);
        
        $now = time();
        list ($begin, $end) = $this->range;
        if ($now - $begin > $timestamp || $now + $end < $timestamp) {
            $this->addResponseHeaders($now, $this->range);
            throw new BadRequestHttpException($this->errorMessage);
        }
        
        return true;
    }
    
    /**
     * 是否为空。
     * 
     * @param integer $value
     * @return boolean
     */
    protected function isEmpty($value)
    {
        if ($this->isEmpty !== null) {
            return call_user_func($this->isEmpty, $value);
        }
        
        return empty($value);
    }
    
    /**
     * 添加响应头信息。
     * 
     * @param integer $now 当前时间戳。
     * @param array $range 时间戳限制范围。
     */
    public function addResponseHeaders($now, $range)
    {
        if ($this->enableResponseHeaders) {
            $this->response->getHeaders()
                ->set('X-Timestamp-Range-Limit-Now', $now)
                ->set('X-Timestamp-Range-Limit-Range', '[' . implode(',', $range) . ']');
        }
    }
}
