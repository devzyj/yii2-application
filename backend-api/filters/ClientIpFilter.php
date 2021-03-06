<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\filters;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * ClientIpFilter 实现了验证客户端 IP 是否被允许访问。
 * 
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'clientIpFilter' => [
 *             'class' => 'backendApi\filters\ClientIpFilter',
 *         ],
 *     ];
 * }
 * ```
 * 
 * ClientIpFilter 需要 [[user]] 实现 [[ClientIpFilterInterface]]。
 * 如果 [[user]] 没有设置或没有实现 [[ClientIpFilterInterface]]， ClientIpFilter 将什么也不做。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientIpFilter extends \yii\base\ActionFilter
{
    /**
     * @var string 错误信息。
     */
    public $errorMessage = 'IP address limit.';

    /**
     * @var integer 错误编码。
     */
    public $errorCode = 0;
    
    /**
     * @var \yii\web\Request 当前的请求。如果没有设置，将使用 `Yii::$app->getRequest()`。
     */
    public $request;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->request === null) {
            $this->request = Yii::$app->getRequest();
        }
        
        parent::init();
    }
    
    /**
     * {@inheritdoc}
     * 
     * @throws \yii\web\ForbiddenHttpException 客户端 IP 不被允许。
     */
    public function beforeAction($action)
    {
        if (($user = Yii::$app->getUser()) && ($identity = $user->getIdentity(false))) {
            if ($identity instanceof ClientIpFilterInterface) {
                Yii::debug('Check client ip.', __METHOD__);
                
                $ip = $this->request->getUserIP();
                if (!$identity->checkAllowedClientIp($ip, $action, $this->request)) {
                    throw new ForbiddenHttpException($this->errorMessage, $this->errorCode);
                }
            } else {
                Yii::info('Skipped: `user` does not implement ClientIpFilterInterface.', __METHOD__);
            }
        } else {
            Yii::info('Skipped: user not logged in.', __METHOD__);
        }
        
        return true;
    }
}
