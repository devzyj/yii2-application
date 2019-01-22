<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities;

use common\oauth2\server\interfaces\ClientEntityInterface;
use common\oauth2\server\models\OauthClient;
use common\oauth2\server\models\OauthClientScope;

/**
 * ClientEntity class.
 * 
 * @property ScopeEntity[] $oauthScopes 客户端的权限
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientEntity extends OauthClient implements ClientEntityInterface
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthScopes()
    {
        return $this->hasMany(ScopeEntity::className(), ['id' => 'scope_id'])->viaTable(OauthClientScope::tableName(), ['client_id' => 'id']);
    }
    
    /******************************** ClientEntityInterface ********************************/
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGrantTypes()
    {
        return parent::getGrantTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUri()
    {
        return [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAccessTokenDuration()
    {
        return $this->access_token_duration;
    }

    /**
     * {@inheritdoc}
     */
    public function getRefreshTokenDuration()
    {
        return $this->refresh_token_duration;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopeEntities()
    {
        return $this->oauthScopes;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultScopeEntities()
    {
        return $this->oauthScopes;
    }
}