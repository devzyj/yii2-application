<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use common\models\backend\OauthClientScope as CommonBackendOauthClientScope;

/**
 * This is the model class for table "{{%oauth_client_scope}}".
 *
 * @property OauthScope $oauthScope 权限
 * @property OauthClient $oauthClient 客户端
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClientScope extends CommonBackendOauthClientScope
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthScope()
    {
        return $this->hasOne(OauthScope::class, ['id' => 'scope_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthClient()
    {
        return $this->hasOne(OauthClient::class, ['id' => 'client_id']);
    }
}
