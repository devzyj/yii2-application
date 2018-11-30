<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\backend;

use Yii;

/**
 * This is the model class for table "{{%backend_oauth_scope}}".
 *
 * @property int $id ID
 * @property string $scope 权限范围
 * @property string $description 描述
 * @property int $create_time 创建时间
 *
 * @property OauthClientScope[] $clientScopes 客户端与权限范围的关联数据
 * @property OauthClient[] $clients 客户端
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthScope extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_oauth_scope}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scope'], 'required'],
            [['scope'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
            [['scope'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'scope' => 'Scope',
            'description' => 'Description',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * 获取客户端与权限范围的关联数据。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getClientScopes()
    {
        return $this->hasMany(OauthClientScope::class, ['scope_id' => 'id']);
    }

    /**
     * 获取客户端。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(OauthClient::class, ['id' => 'client_id'])->viaTable(OauthClientScope::tableName(), ['scope_id' => 'id']);
    }
}
