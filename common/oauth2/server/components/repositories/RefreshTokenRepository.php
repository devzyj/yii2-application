<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\repositories;

use Yii;
use common\oauth2\server\interfaces\RefreshTokenRepositoryInterface;
use common\oauth2\server\interfaces\RefreshTokenEntityInterface;
use common\oauth2\server\components\entities\RefreshTokenEntity;

/**
 * RefreshTokenRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     * 
     * @return RefreshTokenEntity 新的更新令牌实例。
     */
    public function createRefreshTokenEntity()
    {
        return Yii::createObject(RefreshTokenEntity::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveRefreshToken(RefreshTokenEntityInterface $token)
    {}
    
    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($identifier)
    {}

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($identifier)
    {}
}