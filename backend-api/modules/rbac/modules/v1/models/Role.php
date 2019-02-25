<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_role}}".
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
class Role extends \common\models\rbac\Role
{
    /**
     * @var string 新增数据的场景名称。
     */
    const SCENARIO_INSERT = 'insert';

    /**
     * @var string 更新数据的场景名称。
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // 默认场景。
        $scenarios = parent::scenarios();
    
        // 新增数据的场景。
        $scenarios[self::SCENARIO_INSERT] = [
            'client_id', 'name', 'description', 'status'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'name', 'description', 'status'
        ];
    
        // 返回修改后的场景列表。
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function extraFields()
    {
        return ['client', 'users', 'permissions'];
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
    
    /**
     * {@inheritdoc}
     * 
     * @return RoleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RoleQuery(get_called_class());
    }
}
