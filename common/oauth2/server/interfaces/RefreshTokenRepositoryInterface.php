<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

use common\oauth2\server\components\UniqueTokenIdentifierException;

/**
 * 更新令牌存储接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface RefreshTokenRepositoryInterface
{
    /**
     * 创建新的更新令牌实例。
     * 
     * @return RefreshTokenEntityInterface 新的更新令牌实例。
     */
    public function createRefreshTokenEntity();
    
    /**
     * 保存令牌。
     * 
     * @param RefreshTokenEntityInterface $token 令牌。
     * @throws UniqueTokenIdentifierException 令牌标识重复。
     */
    public function saveRefreshToken(RefreshTokenEntityInterface $token);
    
    /**
     * 撤销令牌。
     * 
     * @param string $identifier 令牌标识。
     * @return boolean 撤销是否成功。
     */
    public function revokeRefreshToken($identifier);

    /**
     * 令牌是否已撤销。
     *
     * @param string $identifier 令牌标识。
     * @return boolean 是否已撤销。
     */
    public function isRefreshTokenRevoked($identifier);
    
}