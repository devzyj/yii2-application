<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

use devjerry\oauth2\server\exceptions\UniqueIdentifierException;

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
     * 生成更新令牌唯一标识。
     *
     * @return string|null 更新令牌唯一标识。如果返回 `null`，则自动生成。
     */
    public function generateRefreshTokenUniqueIdentifier();
    
    /**
     * 保存更新令牌。
     * 
     * @param RefreshTokenEntityInterface $refreshTokenEntity 更新令牌。
     * @throws UniqueIdentifierException 令牌标识重复。
     */
    public function saveRefreshTokenEntity(RefreshTokenEntityInterface $refreshTokenEntity);
    
    /**
     * 撤销更新令牌。
     * 
     * @param string $identifier 更新令牌标识。
     */
    public function revokeRefreshTokenEntity($identifier);

    /**
     * 更新令牌是否已撤销。
     *
     * @param string $identifier 更新令牌标识。
     * @return boolean 是否已撤销。
     */
    public function isRefreshTokenEntityRevoked($identifier);

    /**
     * 序列化更新令牌，用于最终的响应结果。
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity 更新令牌。
     * @param mixed $cryptKey 更新令牌密钥。
     * @return string
     */
    public function serializeRefreshTokenEntity(RefreshTokenEntityInterface $refreshTokenEntity, $cryptKey);
    
    /**
     * 反序列化更新令牌，用于从请求中接收到的更新令牌。
     *
     * 返回的实例必需要设置的属性如下：
     *     - [[setIdentifier()]]
     *     - [[setExpires()]]
     *     - [[setAccessTokenIdentifier()]]
     *     - [[setClientIdentifier()]]
     *     - [[setUserIdentifier()]] 如果没有用户，可以不设置。在客户端授权模式中没有用户。
     *     - [[addScopeIdentifier()]]
     * 
     * @param string $serializedRefreshToken 已序列化的更新令牌。
     * @param mixed $cryptKey 更新令牌密钥。
     * @return RefreshTokenEntityInterface 更新令牌实例。
     */
    public function unserializeRefreshTokenEntity($serializedRefreshToken, $cryptKey);
}