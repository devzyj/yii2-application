<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 访问令牌实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AccessTokenEntityInterface
{
    /**
     * 获取访问令牌的标识符。
     *
     * @return string 访问令牌的标识符。
     */
    public function getIdentifier();
    
    /**
     * 设置访问令牌的标识符。
     *
     * @param string $identifier 访问令牌的标识符。
     */
    public function setIdentifier($identifier);

    /**
     * 获取访问令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires();
    
    /**
     * 设置访问令牌的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires);
    
    /**
     * 获取与访问令牌关联的客户端。
     *
     * @return ClientEntityInterface 客户端实例。
     */
    public function getClientEntity();
    
    /**
     * 设置与访问令牌关联的客户端。
     * 
     * @param ClientEntityInterface $clientEntity 客户端实例。
     */
    public function setClientEntity(ClientEntityInterface $clientEntity);

    /**
     * 获取与访问令牌关联的用户。
     *
     * @return UserEntityInterface 用户实例。
     */
    public function getUserEntity();
    
    /**
     * 设置与访问令牌关联的用户。
     *
     * @param UserEntityInterface $userEntity 用户实例。
     */
    public function setUserEntity(UserEntityInterface $userEntity);

    /**
     * 获取与访问令牌关联的权限。
     *
     * @return ScopeEntityInterface[] 权限实例列表。
     */
    public function getScopeEntities();

    /**
     * 添加与访问令牌关联的权限。
     * 
     * @param ScopeEntityInterface $scopeEntity 权限实例。
     */
    public function addScopeEntity(ScopeEntityInterface $scopeEntity);
}