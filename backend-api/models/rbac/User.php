<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models\rbac;

use Yii;

/**
 * This is the model class for table "{{%rbac_user}}".
 *
 * @property int $id 用户ID
 * @property int $client_id 客户端ID
 * @property string $account 用户标识
 * @property string $description 用户描述
 * @property int $create_time 创建时间
 *
 * @property Client $client 客户端
 * @property RoleUser[] $roleUsers  角色与用户关联
 * @property Role[] $roles 角色
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_user}}';
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
            [['client_id', 'account'], 'required'],
            [['client_id'], 'integer'],
            [['account'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
            [['account'], 'unique', 'targetAttribute' => ['client_id', 'account']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'account' => 'Account',
            'description' => 'Description',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * 客户端查询对像。
     * 
     * @return ClientQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleUsers()
    {
        return $this->hasMany(RoleUser::class, ['user_id' => 'id']);
    }

    /**
     * 角色查询对像。
     * 
     * @return RoleQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->viaTable(RoleUser::tableName(), ['user_id' => 'id']);
    }
}
