<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\rbac;

use Yii;

/**
 * This is the model class for table "{{%rbac_permission_role}}".
 *
 * @property int $permission_id 权限ID
 * @property int $role_id 角色ID
 * @property int $create_time 创建时间
 *
 * @property Permission $permission 权限
 * @property Role $role 角色
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class PermissionRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_permission_role}}';
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
            [['permission_id', 'role_id'], 'integer', 'integerOnly' => true],
            [['permission_id', 'role_id'], 'unique', 'targetAttribute' => ['permission_id', 'role_id']],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => Permission::class, 'targetAttribute' => ['permission_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permission_id' => Yii::t('app', 'Permission ID'),
            'role_id' => Yii::t('app', 'Role ID'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }

    /**
     * 权限查询对像。
     * 
     * @return PermissionQuery
     */
    public function getPermission()
    {
        return $this->hasOne(Permission::class, ['id' => 'permission_id']);
    }

    /**
     * 角色查询对像。
     * 
     * @return RoleQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }
}
