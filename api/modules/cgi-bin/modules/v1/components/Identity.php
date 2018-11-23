<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBinV1\components;

use Yii;
use yii\filters\RateLimitInterface;
use api\components\filters\ClientStatusFilterInterface;
use api\components\filters\ClientIpFilterInterface;
use api\components\traits\RateLimitTrait;

/**
 * 访问接口的客户端标识类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Identity extends \api\components\Identity implements RateLimitInterface, ClientStatusFilterInterface, ClientIpFilterInterface
{
    use RateLimitTrait;
    
    /******************************* IdentityInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var $module \apiAuthorize\Module */
        $module = Yii::$app->getModule('authorize');
        $tokenData = $module->getToken()->getAccessTokenData($token);
        if (isset($tokenData['client_id'])) {
            return static::findOrSetOneById($tokenData['client_id']);
        }
    }

    /******************************* RateLimitInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function getRateLimit($request, $action)
    {
        return $this->getRateLimitContents();
    }

    /******************************* ClientStatusFilterInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function checkClientStatus($action, $request)
    {
        return $this->getClientIsValid();
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
