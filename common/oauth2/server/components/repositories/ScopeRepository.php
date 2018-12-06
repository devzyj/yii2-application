<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\repositories;

use common\oauth2\server\interfaces\ScopeRepositoryInterface;
use common\oauth2\server\components\entities\ScopeEntity;
use common\oauth2\server\interfaces\ClientEntityInterface;
use common\oauth2\server\interfaces\UserEntityInterface;
use yii\helpers\ArrayHelper;

/**
 * ScopeRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * {@inheritdoc}
     * 
     * @return ScopeEntity 权限实例。
     */
    public function getScopeEntity($identifier)
    {
        return ScopeEntity::findOneByIdentifier($identifier);
    }
    
    /**
     * {@inheritdoc}
     */
    public function finalize(array $scopes, $grantType, ClientEntityInterface $client, UserEntityInterface $user = null)
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
        if (empty($scopes)) {
            // 如果没有请求权限，默认使用客户端的全部权限。
            return $client->getScopes();
        }
        
        // 使用标识索引权限数组。
        $scopes = ArrayHelper::index($scopes, function ($element) {
            /* @var $element ScopeEntityInterface */
            return $element->getIdentifier();
        });
        
        // 获取使用标识索引的客户端权限数组。
        $clientScopes = ArrayHelper::index($client->getScopes(), function ($element) {
            /* @var $element ScopeEntityInterface */
            return $element->getIdentifier();
        });
        
        // 检查权限是否有效。
        $result = [];
        foreach ($scopes as $identifier => $scope) {
            if (isset($clientScopes[$identifier])) {
                $result[] = $scope;
            }
        }
        
        // 返回有效的权限。
        return $result;
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
        // TODO 具体实现可以继承后实现该方法。
        return $scopes;
    }
}