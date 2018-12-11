<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories;

use Yii;
use common\oauth2\server\interfaces\AuthorizationCodeRepositoryInterface;
use common\oauth2\server\interfaces\AuthorizationCodeEntityInterface;
use common\oauth2\server\entities\AuthorizationCodeEntity;

/**
 * AuthorizationCodeRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationCodeRepository implements AuthorizationCodeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createAuthorizationCodeEntity()
    {
        return Yii::createObject(AuthorizationCodeEntity::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveAuthorizationCodeEntity(AuthorizationCodeEntityInterface $accessTokenEntity)
    {}
    
    /**
     * {@inheritdoc}
     */
    public function revokeAuthorizationCodeEntity($identifier)
    {}

    /**
     * {@inheritdoc}
     */
    public function isAuthorizationCodeEntityRevoked($identifier)
    {
        return false;
    }
}