<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

/**
 * 访问令牌、更新令牌、授权码基础接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface BaseTokenCodeEntityInterface
{
    /**
     * 获取标识符。
     *
     * @return string
     */
    public function getIdentifier();
    
    /**
     * 设置标识符。
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * 获取过期时间。
     *
     * @return integer 时间戳。
     */
    public function getExpires();
    
    /**
     * 设置过期时间。
     * 
     * @param integer $expires 时间戳。
     */
    public function setExpires($expires);

    /**
     * 获取关联的客户端标识。
     * 
     * @return string
     */
    public function getClientIdentifier();
    
    /**
     * 设置关联的客户端标识。
     *
     * @param string $clientIdentifier
     */
    public function setClientIdentifier($clientIdentifier);
    
    /**
     * 获取关联的客户端。
     *
     * @return ClientEntityInterface
     */
    public function getClientEntity();
    
    /**
     * 设置关联的客户端。
     * 
     * @param ClientEntityInterface $clientEntity
     */
    public function setClientEntity(ClientEntityInterface $clientEntity);

    /**
     * 获取关联的用户标识。
     *
     * @return string
     */
    public function getUserIdentifier();
    
    /**
     * 设置关联的用户标识。
     * 
     * @param string $userIdentifier
     */
    public function setUserIdentifier($userIdentifier);
    
    /**
     * 获取关联的用户。
     *
     * @return UserEntityInterface
     */
    public function getUserEntity();

    /**
     * 设置关联的用户。
     *
     * @param UserEntityInterface $userEntity
     */
    public function setUserEntity(UserEntityInterface $userEntity);
    
    /**
     * 获取关联的权限标识符。
     *
     * @return string[]
     */
    public function getScopeIdentifiers();
    
    /**
     * 添加关联的权限标识符。
     *
     * @param string $scopeIdentifier
    */
    public function addScopeIdentifier($scopeIdentifier);
    
    /**
     * 获取关联的权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopeEntities();

    /**
     * 添加关联的权限。
     * 
     * @param ScopeEntityInterface $scopeEntity
     */
    public function addScopeEntity(ScopeEntityInterface $scopeEntity);
}