<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

/**
 * 权限范围存储库接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface ScopeRepositoryInterface
{
    /**
     * 获取权限实例。
     * 
     * @param string $identifier 权限标识。
     * @return ScopeEntityInterface 权限实例。
     */
    public function getScopeEntity($identifier);
    
    /**
     * 根据请求的权限列表、权限授予类型、客户端、用户，确定最终授予的权限列表。
     * 
     * @param ScopeEntityInterface[] $scopes 请求的权限列表。
     * @param string $grantType 权限授予类型。
     * @param ClientEntityInterface $client 客户端。
     * @param UserEntityInterface $user 用户。
     * @return ScopeEntityInterface[] 最终授予的权限列表。
     */
    public function finalizeEntities(array $scopes, $grantType, ClientEntityInterface $client, UserEntityInterface $user = null);
}