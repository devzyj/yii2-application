<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories;

use Yii;
use common\oauth2\server\interfaces\RefreshTokenRepositoryInterface;
use common\oauth2\server\interfaces\RefreshTokenEntityInterface;
use common\oauth2\server\entities\RefreshTokenEntity;
use common\oauth2\server\repositories\traits\RefreshTokenRepositoryTrait;

/**
 * RefreshTokenRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    use RefreshTokenRepositoryTrait;
    
    /**
     * {@inheritdoc}
     */
    public function createRefreshTokenEntity()
    {
        return Yii::createObject(RefreshTokenEntity::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveRefreshTokenEntity(RefreshTokenEntityInterface $refreshTokenEntity)
    {}
    
    /**
     * {@inheritdoc}
     */
    public function revokeRefreshTokenEntity($identifier)
    {}

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenEntityRevoked($identifier)
    {
        return false;
    }
}