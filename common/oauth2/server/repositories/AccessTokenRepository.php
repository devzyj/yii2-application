<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories;

use Yii;
use common\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use common\oauth2\server\interfaces\AccessTokenEntityInterface;
use common\oauth2\server\entities\AccessTokenEntity;
use common\oauth2\server\repositories\traits\AccessTokenRepositoryTrait;

/**
 * AccessTokenRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    use AccessTokenRepositoryTrait;
    
    /**
     * {@inheritdoc}
     */
    public function createAccessTokenEntity()
    {
        return Yii::createObject(AccessTokenEntity::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity)
    {}
    
    /**
     * {@inheritdoc}
     */
    public function revokeAccessTokenEntity($identifier)
    {}

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenEntityRevoked($identifier)
    {
        return false;
    }
}