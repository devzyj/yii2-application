<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_permission}}".
 *
 * @property Client $client 客户端
 * @property PermissionRole[] $permissionRoles 权限与角色关联
 * @property Role[] $roles 角色
 * @property OperationPermission[] $operationPermissions 操作与权限关联
 * @property Operation[] $operations 操作
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Permission extends \common\models\rbac\Permission
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
    public function getPermissionRoles()
    {
        return $this->hasMany(PermissionRole::class, ['permission_id' => 'id']);
    }

    /**
     * 角色查询对像。
     * 
     * @return RoleQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->viaTable(PermissionRole::tableName(), ['permission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperationPermissions()
    {
        return $this->hasMany(OperationPermission::class, ['permission_id' => 'id']);
    }

    /**
     * 操作查询对像。
     * 
     * @return PermissionQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operation::class, ['id' => 'operation_id'])->viaTable(OperationPermission::tableName(), ['permission_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return PermissionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PermissionQuery(get_called_class());
    }
}
