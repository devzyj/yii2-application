<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\repositories;

use Yii;
use common\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use common\oauth2\server\interfaces\AccessTokenEntityInterface;
use common\oauth2\server\components\entities\AccessTokenEntity;

/**
 * AccessTokenRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     * 
     * @return AccessTokenEntity 新的访问令牌实例。
     */
    public function createEntity()
    {
        return Yii::createObject(AccessTokenEntity::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function save(AccessTokenEntityInterface $token)
    {}
    
    /**
     * {@inheritdoc}
     */
    public function revoke($identifier)
    {}

    /**
     * {@inheritdoc}
     */
    public function isRevoked($identifier)
    {}
}