<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\rbac;

use Yii;

/**
 * This is the model class for table "{{%rbac_role}}".
 *
 * @property int $id 角色ID
 * @property int $client_id 客户端ID
 * @property string $name 角色名称
 * @property string $description 角色描述
 * @property int $create_time 创建时间
 * @property int $status 角色状态（0=禁用；1=可用）
 *
 * @property Client $client 客户端
 * @property RoleUser[] $roleUsers 角色与用户关联
 * @property User[] $users 用户
 * @property PermissionRole[] $permissionRoles 权限与角色关联
 * @property Permission[] $permissions 权限
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * @var integer 状态 - 禁用的。
     */
    const STATUS_DISABLED = 0;

    /**
     * @var integer 状态 - 启用的。
     */
    const STATUS_ENABLED = 1;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_role}}';
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
            [['client_id', 'name'], 'required'],
            [['client_id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['status'], 'boolean'],
            [['name'], 'unique', 'targetAttribute' => ['client_id', 'name']],
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
            'name' => 'Name',
            'description' => 'Description',
            'create_time' => 'Create Time',
            'status' => 'Status',
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
        return $this->hasMany(RoleUser::class, ['role_id' => 'id']);
    }

    /**
     * 用户查询对像。
     * 
     * @return UserQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable(RoleUser::tableName(), ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissionRoles()
    {
        return $this->hasMany(PermissionRole::class, ['role_id' => 'id']);
    }

    /**
     * 权限查询对像。
     * 
     * @return PermissionQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::class, ['id' => 'permission_id'])->viaTable(PermissionRole::tableName(), ['role_id' => 'id']);
    }
}
