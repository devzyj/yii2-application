<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\backend;

use Yii;

/**
 * This is the model class for table "{{%backend_oauth_client_scope}}".
 *
 * @property int $client_id 客户端 ID
 * @property int $scope_id 权限范围 ID
 * @property int $create_time 创建时间
 *
 * @property OauthScope $scope 权限范围
 * @property OauthClient $client 客户端
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClientScope extends \yii\db\ActiveRecord
{
    /**
     * @var integer 不是默认。
     */
    const IS_DEFAULT_NO = 0;
    
    /**
     * @var integer 是默认。
     */
    const IS_DEFAULT_YES = 1;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_oauth_client_scope}}';
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
            [['client_id', 'scope_id'], 'required'],
            [['client_id', 'scope_id'], 'integer'],
            [['client_id', 'scope_id'], 'unique', 'targetAttribute' => ['client_id', 'scope_id']],
            [['scope_id'], 'exist', 'skipOnError' => true, 'targetClass' => OauthScope::class, 'targetAttribute' => ['scope_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => OauthClient::class, 'targetAttribute' => ['client_id' => 'id']],
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
            'create_time' => 'Create Time',
        ];
    }

    /**
     * 获取权限范围。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getScope()
    {
        return $this->hasOne(OauthScope::class, ['id' => 'scope_id']);
    }

    /**
     * 获取客户端。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(OauthClient::class, ['id' => 'client_id']);
    }
}
