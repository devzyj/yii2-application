<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

/**
 * 用户存储库接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface UserRepositoryInterface
{
    /**
     * 获取用户实例。
     * 
     * @param string $identifier 用户标识。
     */
    public function getUserEntity($identifier);
    
    /**
     * 使用用户认证信息，获取用户实例。
     * 
     * @param string $username 用户名。
     * @param string $password 用户密码。
     * @return UserEntityInterface 用户实例。
     */
    public function getUserEntityByCredentials($username, $password);
}