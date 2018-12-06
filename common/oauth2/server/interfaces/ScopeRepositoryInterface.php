<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 权限范围存储接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface ScopeRepositoryInterface
{
    /**
     * 获取权限。
     * 
     * @param string $identifier 权限标识。
     * @return ScopeEntityInterface 权限实例。
     */
    public function getScopeEntity($identifier);
    
    /**
     * 根据请求的权限列表、授权类型、客户端、用户，确定最终授权的权限列表。
     * 
     * 如果请求的权限为空，可以返回默认的权限。
     * 如果请求的权限不为空，可以判断权限对于客户端和用户的有效性，并增加、删除权限。
     * 
     * @param ScopeEntityInterface[] $scopes 请求的权限列表。
     * @param string $grantType 授权类型。
     * @param ClientEntityInterface $client 客户端。
     * @param UserEntityInterface $user 用户。
     * @return ScopeEntityInterface[] 最终授权的权限列表。
     */
    public function finalize(array $scopes, $grantType, ClientEntityInterface $client, UserEntityInterface $user = null);
}