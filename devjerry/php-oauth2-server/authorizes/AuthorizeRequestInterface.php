<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;

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
     * 设置客户端。
     * 
     * @param ClientEntityInterface $clientEntity 客户端实例。
     */
    public function setClientEntity(ClientEntityInterface $clientEntity);
    
    /**
     * 设置回调地址。
     * 
     * @param string $redirectUri 回调地址。
     */
    public function setRedirectUri($redirectUri);
    
    /**
     * 设置请求的状态参数。
     * 
     * @param string $state
     */
    public function setState($state);
    
    /**
     * 设置权限。
     * 
     * @param ScopeEntityInterface[] $scopeEntities 权限。
     */
    public function setScopeEntities(array $scopeEntities);
}