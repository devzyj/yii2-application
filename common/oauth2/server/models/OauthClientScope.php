<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\models;

use Yii;

/**
 * This is the model class for table "{{%oauth_client_scope}}".
 *
 * @property int $client_id 客户端 ID
 * @property int $scope_id 权限 ID
 *
 * @property OauthScope $oauthScope 权限
 * @property OauthClient $oauthClient 客户端
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClientScope extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%oauth_client_scope}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'scope_id'], 'required'],
            [['client_id', 'scope_id'], 'integer'],
            [['client_id', 'scope_id'], 'unique', 'targetAttribute' => ['client_id', 'scope_id']],
            [['scope_id'], 'exist', 'skipOnError' => true, 'targetClass' => OauthScope::className(), 'targetAttribute' => ['scope_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => OauthClient::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Client ID',
            'scope_id' => 'Scope ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthScope()
    {
        return $this->hasOne(OauthScope::className(), ['id' => 'scope_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthClient()
    {
        return $this->hasOne(OauthClient::className(), ['id' => 'client_id']);
    }
}
