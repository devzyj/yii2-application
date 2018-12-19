<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;

/**
 * 授权请求接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AuthorizeRequestInterface
{
    /**
     * 获取创建当前授权请求的授权类型实例。
     * 
     * @return AuthorizeTypeInterface 授权类型实例。
     */
    public function getAuthorizeType();
    
    /**
     * 设置创建当前授权请求的授权类型实例。
     * 
     * @param AuthorizeTypeInterface $authorizeType 授权类型实例。
     */
    public function setAuthorizeType(AuthorizeTypeInterface $authorizeType);

    /**
     * 获取客户端。
     *
     * @return ClientEntityInterface 客户端实例。
     */
    public function getClientEntity();
    
    /**
     * 设置客户端。
     * 
     * @param ClientEntityInterface $clientEntity 客户端实例。
     */
    public function setClientEntity(ClientEntityInterface $clientEntity);

    /**
     * 获取回调地址。
     *
     * @return string 回调地址。
     */
    public function getRedirectUri();
    
    /**
     * 设置回调地址。
     * 
     * @param string $redirectUri 回调地址。
     */
    public function setRedirectUri($redirectUri);

    /**
     * 获取请求的状态参数。
     *
     * @return string
     */
    public function getState();
    
    /**
     * 设置请求的状态参数。
     * 
     * @param string $state
     */
    public function setState($state);

    /**
     * 获取权限。
     *
     * @return ScopeEntityInterface[] 权限。
     */
    public function getScopeEntities();

    /**
     * 添加权限。
     *
     * @param ScopeEntityInterface $scopeEntities 权限。
     */
    public function addScopeEntity(ScopeEntityInterface $scopeEntity);
    
    /**
     * 设置权限。
     * 
     * @param ScopeEntityInterface[] $scopeEntities 权限。
     */
    public function setScopeEntities(array $scopeEntities);

    /**
     * 获取用户。
     *
     * @return UserEntityInterface 用户实例。
     */
    public function getUsertEntity();
    
    /**
     * 设置用户。
     *
     * @param UserEntityInterface $clientEntity 用户实例。
    */
    public function setUserEntity(UserEntityInterface $userEntity);
    
    /**
     * 获取用户是否批准授权。
     * 
     * @return boolean
     */
    public function getIsApproved();
    
    /**
     * 设置用户是否批准授权。
     * 
     * @param boolean $approved
     */
    public function setIsApproved($approved);

    /**
     * 获取交换验证代码。
     *
     * @return string
     */
    public function getCodeChallenge();
    
    /**
     * 设置交换验证代码。
     *
     * @param string $codeChallenge
    */
    public function setCodeChallenge($codeChallenge);

    /**
     * 获取交换验证方法。
     *
     * @return string
     */
    public function getCodeChallengeMethod();
    
    /**
     * 设置交换验证方法。
     *
     * @param string $codeChallengeMethod
    */
    public function setCodeChallengeMethod($codeChallengeMethod);
    
}