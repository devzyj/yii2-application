<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

use common\oauth2\server\components\JwtSignKey;

/**
 * 访问令牌实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AccessTokenEntityInterface
{
    /**
     * 获取令牌的标识符。
     *
     * @return string 令牌的标识符。
     */
    public function getIdentifier();
    
    /**
     * 设置令牌的标识符。
     *
     * @param string $identifier 令牌的标识符。
     */
    public function setIdentifier($identifier);

    /**
     * 获取令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires();
    
    /**
     * 设置令牌的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires);
    
    /**
     * 获取与令牌关联的客户端。
     *
     * @return ClientEntityInterface
     */
    public function getClient();
    
    /**
     * 设置与令牌关联的客户端。
     * 
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client);
    
    /**
     * 获取与令牌关联的权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes();

    /**
     * 添加与令牌关联的权限。
     * 
     * @param ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope);

    /**
     * 获取与令牌关联的用户。
     *
     * @return UserEntityInterface
     */
    public function getUser();
    
    /**
     * 设置与令牌关联的用户。
     *
     * @param UserEntityInterface $user
     */
    public function setUser(UserEntityInterface $user);

    /**
     * 转换成 JWT。
     *
     * @param JwtSignKey $key
     * @return string
     */
    public function convertToJWT(JwtSignKey $key);
}