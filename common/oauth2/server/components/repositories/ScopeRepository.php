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
    public function getEntity($identifier)
    {
        return ScopeEntity::findOneByIdentifier($identifier);
    }
    
    /**
     * {@inheritdoc}
     */
    public function finalize(array $scopes, $grantType, ClientEntityInterface $client, UserEntityInterface $user = null)
    {
        if ($grantType === 'client_credentials' && empty($scopes)) {
            // 客户端授权时，如果没有请求权限，默认使用客户端的全部权限。
            return $client->getScopes();
        }
        
        return $scopes;
    }
}