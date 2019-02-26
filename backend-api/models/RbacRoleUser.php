<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_role_user}}".
 *
 * @property int $role_id 角色 ID
 * @property int $user_id 用户 ID
 * @property int $create_time 创建时间
 *
 * @property RbacUser $rbacUser 用户
 * @property RbacRole $rbacRole 角色
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacRoleUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_role_user}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_backend');
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
            [['role_id', 'user_id'], 'required'],
            [['role_id', 'user_id'], 'integer'],
            [['role_id', 'user_id'], 'unique', 'targetAttribute' => ['role_id', 'user_id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacRole::class, 'targetAttribute' => ['role_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacUser::class, 'targetAttribute' => ['User' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'role_id' => 'Role ID',
            'user_id' => 'User ID',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * 获取用户查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacUser()
    {
        return $this->hasOne(RbacUser::class, ['id' => 'user_id']);
    }

    /**
     * 获取角色查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRole()
    {
        return $this->hasOne(RbacRole::class, ['id' => 'role_id']);
    }
}
