<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_permission_role}}".
 *
 * @property int $permission_id 权限 ID
 * @property int $role_id 角色 ID
 * @property int $create_time 创建时间
 *
 * @property RbacRole $rbacRole 角色
 * @property RbacPermission $rbacPermission 权限
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacPermissionRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_permission_role}}';
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
            [['permission_id', 'role_id'], 'required'],
            [['permission_id', 'role_id'], 'integer'],
            [['permission_id', 'role_id'], 'unique', 'targetAttribute' => ['permission_id', 'role_id']],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacPermission::class, 'targetAttribute' => ['permission_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacRole::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permission_id' => 'Permission ID',
            'role_id' => 'Role ID',
            'create_time' => 'Create Time',
        ];
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

    /**
     * 获取权限查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermission()
    {
        return $this->hasOne(RbacPermission::class, ['id' => 'permission_id']);
    }
}
