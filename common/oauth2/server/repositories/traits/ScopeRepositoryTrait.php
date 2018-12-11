<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories\traits;

use yii\helpers\ArrayHelper;
use common\oauth2\server\interfaces\ClientEntityInterface;
use common\oauth2\server\interfaces\UserEntityInterface;
use common\oauth2\server\interfaces\ScopeEntityInterface;

/**
 * ScopeRepositoryTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait ScopeRepositoryTrait
{
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
    public function finalizeEntities(array $scopes, $grantType, ClientEntityInterface $client, UserEntityInterface $user = null)
    {
        if ($grantType === 'client_credentials') {
            // 客户端授权模式，确认客户端的权限。
            return $this->ensureClientCredentials($scopes, $client);
        } elseif ($grantType === 'password') {
            // 用户密码授权模式，确认用户的权限。
            return $this->ensureUserCredentials($scopes, $client, $user);
        }
        
        return $scopes;
    }
    
    /**
     * 确认客户端的权限。
     * 
     * @param ScopeEntityInterface[] $scopes 请求的权限列表。 
     * @param ClientEntityInterface $client 客户端。
     * @return ScopeEntityInterface[] 有效的权限列表。
     */
    protected function ensureClientCredentials(array $scopes, ClientEntityInterface $client)
    {
        return $this->ensureScopes($scopes, $client);
    }
    
    /**
     * 确认用户的权限。
     * 
     * @param ScopeEntityInterface[] $scopes 请求的权限列表。 
     * @param ClientEntityInterface $client 客户端。
     * @param UserEntityInterface $user 用户。
     * @return ScopeEntityInterface[] 有效的权限列表。
     */
    protected function ensureUserCredentials(array $scopes, ClientEntityInterface $client, UserEntityInterface $user)
    {
        return $this->ensureScopes($scopes, $user);
    }

    /**
     * 确认客户端或者用户的有效权限。
     *
     * @param ScopeEntityInterface[] $scopes 请求的权限列表。
     * @param ClientEntityInterface|UserEntityInterface $entity 客户端或者用户。
     * @return ScopeEntityInterface[] 有效的权限列表。
     */
    protected function ensureScopes(array $scopes, $entity)
    {
        if (empty($scopes)) {
            // 如果没有请求权限，使用默认权限。
            return $entity->getDefaultScopeEntities();
        }
        
        // 使用标识索引权限数组。
        $scopes = ArrayHelper::index($scopes, function ($element) {
            /* @var $element ScopeEntityInterface */
            return $element->getIdentifier();
        });
        
        // 获取使用标识索引的全部权限的数组。
        $scopeEntities = ArrayHelper::index($entity->getScopeEntities(), function ($element) {
            /* @var $element ScopeEntityInterface */
            return $element->getIdentifier();
        });
        
        // 检查权限是否有效。
        $result = [];
        foreach ($scopes as $identifier => $scope) {
            if (isset($scopeEntities[$identifier])) {
                $result[] = $scope;
            }
        }
        
        // 返回有效的权限。
        return $result;
    }
}