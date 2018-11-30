<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiOauth\components;

use Yii;
use yii\filters\RateLimitInterface;
use backendApi\components\filters\ClientIpFilterInterface;
use common\traits\RateLimitTrait;

/**
 * 访问接口的客户端标识类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Identity extends \backendApi\components\Identity implements RateLimitInterface, ClientIpFilterInterface
{
    use RateLimitTrait;
    
    /******************************* RateLimitInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function getRateLimit($request, $action)
    {
        return Yii::$app->params['rateLimit'];
    }

    /******************************* ClientIpFilterInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function checkClientIp($ip, $action, $request)
    {
        return $this->checkClientAllowedIp($ip);
    }
}
