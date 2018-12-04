<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities;

use common\oauth2\server\components\Key;

/**
 * 访问令牌实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AccessTokenEntityInterface
{
    /**
     * 设置标识。
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * 获取标识。
     *
     * @return string
     */
    public function getIdentifier();
    
    /**
     * 设置过期时间。
     * 
     * @param integer $timestamp
     */
    public function setExpires($timestamp);
    
    /**
     * 获取过期时间。
     *
     * @return integer
     */
    public function getExpires();
    
    /**
     * 设置客户端。
     * 
     * @param ClientEntityInterface $client
     */
    public function setClient($client);
    
    /**
     * 获取客户端。
     * 
     * @return ClientEntityInterface
     */
    public function getClient();
    
    /**
     * 设置权限。
     *
     * @param ScopeEntityInterface[] $scopes
     */
    public function setScopes($scopes);

    /**
     * 获取权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes();
    
    /**
     * 设置用户。
     *
     * @param UserEntityInterface $user
     */
    public function setUser($user);

    /**
     * 获取用户。
     *
     * @return UserEntityInterface
     */
    public function getUser();

    /**
     * 从访问令牌生成 JWT。
     *
     * @param Key $privateKey
     * @return string
     */
    public function convertToJWT($privateKey);
}