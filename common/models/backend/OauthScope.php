<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\backend;

use Yii;
use devzyj\yii2\oauth2\server\models\OauthScope as DevzyjOauthScope;

/**
 * This is the model class for table "{{%oauth_scope}}".
 *
 * @property OauthClientScope[] $oauthClientScopes 客户端与权限的关联关系
 * @property OauthClient[] $oauthClients 客户端
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
     * @return \yii\db\ActiveQuery
     */
    public function getOauthClientScopes()
    {
        return $this->hasMany(OauthClientScope::class, ['scope_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthClients()
    {
        return $this->hasMany(OauthClient::class, ['id' => 'client_id'])->viaTable(OauthClientScope::tableName(), ['scope_id' => 'id']);
    }
}
