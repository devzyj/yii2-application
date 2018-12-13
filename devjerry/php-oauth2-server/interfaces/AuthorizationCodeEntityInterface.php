<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

/**
 * 授权码实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AuthorizationCodeEntityInterface
{
    /**
     * 获取授权码的标识符。
     *
     * @return string 授权码的标识符。
     */
    public function getIdentifier();
    
    /**
     * 设置授权码的标识符。
     *
     * @param string $identifier 授权码的标识符。
     */
    public function setIdentifier($identifier);

    /**
     * 获取授权码的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires();
    
    /**
     * 设置授权码的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires);

    /**
     * 获取授权码的回调地址。
     *
     * @return string 回调地址。
     */
    public function getRedirectUri();
    
    /**
     * 设置授权码的回调地址。
     *
     * @param string $redirectUri 回调地址。
     */
    public function setRedirectUri($redirectUri);

    /**
     * 获取与授权码关联的客户端标识。
     *
     * @return string 客户端标识。
     */
    public function getClientIdentifier();
    
    /**
     * 设置与授权码关联的客户端标识。
     *
     * @param string $clientIdentifier 客户端标识。
     */
    public function setClientIdentifier($clientIdentifier);
    
    /**
     * 获取与授权码关联的用户标识。
     *
     * @return string 用户标识。
     */
    public function getUserIdentifier();
    
    /**
     * 设置与授权码关联的用户标识。
     *
     * @param string $userIdentifier 用户标识。
     */
    public function setUserIdentifier($userIdentifier);
    
    /**
     * 获取与授权码关联的权限标识符。
     *
     * @return string[] 权限标识符列表。
     */
    public function getScopeIdentifiers();
    
    /**
     * 添加与授权码关联的权限标识符。
     *
     * @param string $scopeIdentifier 权限标识符。
     */
    public function addScopeIdentifier($scopeIdentifier);
    
    /**
     * 获取交换验证代码。
     *
     * @return string 交换验证代码。
     */
    public function getCodeChallenge();
    
    /**
     * 设置交换验证代码。
     *
     * @param string $codeChallenge 交换验证代码。
     */
    public function setCodeChallenge($codeChallenge);

    /**
     * 获取交换验证方法。
     *
     * @return string 交换验证方法。
     */
    public function getCodeChallengeMethod();
    
    /**
     * 设置交换验证方法。
     *
     * @param string $codeChallengeMethod 交换验证方法。
     */
    public function setCodeChallengeMethod($codeChallengeMethod);
}