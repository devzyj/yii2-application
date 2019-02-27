<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;
use devzyj\yii2\oauth2\server\models\OauthScope as DevzyjOauthScope;

/**
 * This is the model class for table "{{%oauth_scope}}".
 *
 * @property OauthClientScope[] $oauthClientScopes 客户端与权限的关联关系
 * @property OauthClient[] $oauthClients 客户端
 * @property OauthScopeContent $oauthScopeContent 权限内容
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthScope extends DevzyjOauthScope
{
    /**
     * {@inheritdoc}
     */
    public static function getDb()
    {
        return Yii::$app->get('db_backend');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOauthClientScopes()
    {
        return $this->hasMany(OauthClientScope::class, ['scope_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function getOauthClients()
    {
        return $this->hasMany(OauthClient::class, ['id' => 'client_id'])->viaTable(OauthClientScope::tableName(), ['scope_id' => 'id']);
    }
    
    /**
     * 获取权限内容查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getOauthScopeContent()
    {
        return $this->hasOne(OauthScopeContent::class, ['scope_id' => 'id']);
    }
    
    /**
     * 检查 API 是否被允许。
     *
     * @param string $api 需要检查的 API。
     * @return boolean
     */
    public function checkAllowedApi($api)
    {
        if ($this->oauthScopeContent && $this->oauthScopeContent->checkAllowedApi($api)) {
            return true;
        }
        
        return false;
    }
}
