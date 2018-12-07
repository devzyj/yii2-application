<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

use common\oauth2\server\exceptions\UniqueTokenIdentifierException;

/**
 * 更新令牌存储库接口。
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
     * 保存更新令牌。
     * 
     * @param RefreshTokenEntityInterface $refreshToken 更新令牌。
     * @throws UniqueTokenIdentifierException 令牌标识重复。
     */
    public function saveRefreshToken(RefreshTokenEntityInterface $refreshToken);
    
    /**
     * 撤销更新令牌。
     * 
     * @param string $identifier 更新令牌标识。
     * @return boolean 撤销是否成功。
     */
    public function revokeRefreshToken($identifier);

    /**
     * 更新令牌是否已撤销。
     *
     * @param string $identifier 更新令牌标识。
     * @return boolean 是否已撤销。
     */
    public function isRefreshTokenRevoked($identifier);

    /**
     * 序列化更新令牌，用于最终的响应结果。
     *
     * @param RefreshTokenEntityInterface $refreshToken 更新令牌。
     * @return string
     */
    public function serializeRefreshToken(RefreshTokenEntityInterface $refreshToken);
    
    /**
     * 反序列化更新令牌，用于从请求中接收到的更新令牌。
     *
     * @param string $serializedRefreshToken 已序列化的更新令牌。
     * @return RefreshTokenEntityInterface 更新令牌实例。
    */
    public function unserializeRefreshToken($serializedRefreshToken);
}