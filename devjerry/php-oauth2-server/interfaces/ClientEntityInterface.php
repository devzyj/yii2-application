<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

/**
 * 客户端实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface ClientEntityInterface
{
    /**
     * 获取客户端标识符。
     * 
     * @return string
     */
    public function getIdentifier();

    /**
     * 获取客户端回调地址。
     *
     * @return string|string[]
     */
    public function getRedirectUri();
    
    /**
     * 获取客户端权限授予类型。
     * 
     * @return string[] 如果返回值不是数组，则不进行验证。
     */
    public function getGrantTypes();

    /**
     * 获取访问令牌的持续时间（秒）。
     * 
     * @return integer|null 如果返回 `null`，则使用全局的持续时间。
     */
    public function getAccessTokenDuration();

    /**
     * 获取更新令牌的持续时间（秒）。
     *
     * @return integer|null 如果返回 `null`，则使用全局的持续时间。
     */
    public function getRefreshTokenDuration();
    
    /**
     * 获取客户端默认权限。
     *
     * 只有在 `client_credentials` 授予模式时才会生效。
     *
     * @return ScopeEntityInterface[] 如果返回值不是数组，则使用全局的默认权限。
     */
    public function getDefaultScopeEntities();
    
    /**
     * 获取客户端全部权限。
     * 
     * @return ScopeEntityInterface[]
     * @deprecated
     */
    public function getScopeEntities();
}