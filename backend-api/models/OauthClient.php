<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use common\models\backend\OauthClient as CommonBackendOauthClient;

/**
 * This is the model class for table "{{%oauth_client}}".
 *
 * @property OauthClientScope[] $oauthClientScopes 客户端与权限的关联关系
 * @property OauthScope[] $oauthScopes 客户端的权限
 * @property OauthScope[] $defaultOauthScopes 客户端的默认权限
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClient extends CommonBackendOauthClient
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthClientScopes()
    {
        return $this->hasMany(OauthClientScope::class, ['client_id' => 'id']);
    }
    
    /**
     * 获取客户端的权限。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getOauthScopes()
    {
        return $this->hasMany(OauthScope::class, ['id' => 'scope_id'])->viaTable(OauthClientScope::tableName(), ['client_id' => 'id']);
    }

    /**
     * 获取客户端的默认权限。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultOauthScopes()
    {
        return $this->hasMany(OauthScope::class, ['id' => 'scope_id'])->viaTable(OauthClientScope::tableName(), ['client_id' => 'id'], function ($query) {
            $query->andWhere(['is_default' => OauthClientScope::IS_DEFAULT_YES]);
        });
    }
}
