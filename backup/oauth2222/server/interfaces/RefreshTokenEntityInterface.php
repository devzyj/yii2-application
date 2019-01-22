<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 更新令牌实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface RefreshTokenEntityInterface
{
    /**
     * 获取更新令牌的标识符。
     *
     * @return string 更新令牌的标识符。
     */
    public function getIdentifier();
    
    /**
     * 设置更新令牌的标识符。
     *
     * @param string $identifier 更新令牌的标识符。
     */
    public function setIdentifier($identifier);

    /**
     * 获取更新令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires();
    
    /**
     * 设置更新令牌的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires);

    /**
     * 获取与更新令牌关联的客户端标识。
     *
     * @return string 客户端标识。
     */
    public function getClientIdentifier();
    
    /**
     * 设置与更新令牌关联的客户端标识。
     *
     * @param string $clientIdentifier 客户端标识。
     */
    public function setClientIdentifier($clientIdentifier);
    
    /**
     * 获取与更新令牌关联的用户标识。
     *
     * @return string 用户标识。
     */
    public function getUserIdentifier();
    
    /**
     * 设置与更新令牌关联的用户标识。
     *
     * @param string $userIdentifier 用户标识。
     */
    public function setUserIdentifier($userIdentifier);
    
    /**
     * 获取与更新令牌关联的权限标识符。
     *
     * @return string[] 权限标识符列表。
     */
    public function getScopeIdentifiers();
    
    /**
     * 添加与更新令牌关联的权限标识符。
     *
     * @param string $scopeIdentifier 权限标识符。
     */
    public function addScopeIdentifier($scopeIdentifier);

    /**
     * 获取与更新令牌关联的访问令牌标识。
     *
     * @param string 访问令牌标识。
     */
    public function getAccessTokenIdentifier();
    
    /**
     * 设置与更新令牌关联的访问令牌标识。
     * 
     * @param string $accessTokenIdentifier 访问令牌标识。
     */
    public function setAccessTokenIdentifier($accessTokenIdentifier);
    
    /**
     * 获取与更新令牌关联的访问令牌。
     *
     * @return AccessTokenEntityInterface 访问令牌实例。
     */
    public function getAccessTokenEntity();
    
    /**
     * 设置与更新令牌关联的访问令牌。
     * 
     * @param AccessTokenEntityInterface $accessTokenEntity 访问令牌实例。
     */
    public function setAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity);
}