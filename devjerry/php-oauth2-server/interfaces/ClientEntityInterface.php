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
     * 获取客户端授权类型。
     * 
     * @return string[]|null 如果返回 `null`，则不进行验证。
     */
    public function getGrantTypes();

    /**
     * 获取客户端回调地址。
     *
     * @return string|string[]
     */
    public function getRedirectUri();

    /**
     * 获取访问令牌的持续时间（秒）。
     * 
     * @return integer
     */
    public function getAccessTokenDuration();

    /**
     * 获取更新令牌的持续时间（秒）。
     *
     * @return integer
     */
    public function getRefreshTokenDuration();
    
    /**
     * 获取客户端全部权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopeEntities();
    
    /**
     * 获取客户端默认权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getDefaultScopeEntities();
}