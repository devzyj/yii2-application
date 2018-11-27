<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backup\components\filters;

use Yii;
use yii\web\UnauthorizedHttpException;

/**
 * SignatureFilter 实现了验证请求中签名参数的有效性。
 * 
 * 当签名参数不正确时，会抛出一个 [[\yii\web\UnauthorizedHttpException]] 异常。
 * 
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'signatureFilter' => [
 *             'class' => 'api\components\filters\SignatureFilter',
 *             'enableResponseHeaders' => YII_DEBUG,
 *         ],
 *     ];
 * }
 * ```
 *
 * SignatureFilter 需要 [[$identity]] 实现 [[SignatureFilterInterface]]。
 * 如果 [[$identity]] 未设置或者未实现 [[SignatureFilterInterface]]]，则什么也不做。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 * @deprecated
 */
class SignatureFilter extends \yii\base\ActionFilter
{
    /**
     * @var bool 是否在响应中包含签名验证标头。
     */
    public $enableResponseHeaders = false;
    
    /**
     * @var string|callable 传递签名的参数名，或者是一个返回签名的回调函数。
     * 
     * ```php
     * function ($request, $action) {
     *     // $request 当前请求对像。
     *     // $action 将要执行的动作。
     * }
     * ```
     */
    public $paramName = 'sign';

    /**
     * @var boolean 签名为空时是否跳过检查。
     */
    public $skipOnEmpty = false;
    
    /**
     * @var callable 判断签名是否为空的回调函数。函数应该返回一个 `boolean` 值。
     *
     * ```php
     * function ($value) {
     *     // $value 需要判断的签名。
     * }
     * ```
     */
    public $isEmpty;
    
    /**
     * @var string 当签名不正确时显示的消息。
     */
    public $errorMessage;
    
    /**
     * @var \yii\web\Request 当前的请求。如果没有设置，将使用 `request` 应用程序组件。
     */
    public $request;
    
    /**
     * @var \yii\web\Response 要发送的响应。如果没有设置，将使用 `response` 应用程序组件。
     */
    public $response;

    /**
     * @var SignatureFilterInterface 实现了 `SignatureFilterInterface` 的用户对像。
     * 如果没有设置，则使用 `Yii::$app->user->getIdentity(false)`。
     */
    public $identity;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->errorMessage === null) {
            $this->errorMessage = 'The signature of the request is invalid.';
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
     */
    public function beforeAction($action)
    {
        if (is_string($this->paramName)) {
            $sign = $this->request->getQueryParam($this->paramName);
        } else {
            $sign = call_user_func($this->paramName, $this->request, $action);
        }
        
        if ($this->skipOnEmpty && $this->isEmpty($sign)) {
            Yii::debug('Skipped: signature is empty.', __METHOD__);
            return true;
        }
        
        if ($this->identity === null && Yii::$app->getUser()) {
            $this->identity = Yii::$app->getUser()->getIdentity(false);
        }
        
        if ($this->identity instanceof SignatureFilterInterface) {
            Yii::debug('Check signature.', __METHOD__);
            
            $realSign = $this->identity->getRealSignature($this->request, $action);
            if ($sign != $realSign) {
                $this->addResponseHeaders($realSign);
                throw new UnauthorizedHttpException($this->errorMessage);
            }
        } elseif ($this->identity) {
            Yii::info('Skipped: "user" does not implement SignatureFilterInterface.', __METHOD__);
        } else {
            Yii::info('Skipped: user not logged in.', __METHOD__);
        }
        
        return true;
    }
    
    /**
     * 是否为空。
     *
     * @param string $value
     * @return boolean
     */
    protected function isEmpty($value)
    {
        if ($this->isEmpty !== null) {
            return call_user_func($this->isEmpty, $value);
        }
        
        return $value === null || $value === '';
    }
    
    /**
     * 添加响应头信息。
     * 
     * @param string $realSign 真实的签名。
     */
    public function addResponseHeaders($realSign)
    {
        if ($this->enableResponseHeaders) {
            $this->response->getHeaders()->set('X-Signature-Filter-Real', $realSign);
        }
    }
}
