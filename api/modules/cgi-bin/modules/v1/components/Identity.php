<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBinV1\components;

use Yii;
use yii\filters\RateLimitInterface;
use api\components\traits\RateLimitTrait;

/**
 * 访问接口的客户端标识类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Identity extends \api\components\Identity implements RateLimitInterface
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
        $jwt = $module->getToken()->getClientCredentials($token);
        if ($jwt && $jwt->hasClaim('client_id')) {
            $clientId = $jwt->getClaim('client_id');
            return static::findOrSetOneById($clientId);
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
}
