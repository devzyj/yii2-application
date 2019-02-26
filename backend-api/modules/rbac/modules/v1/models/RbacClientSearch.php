<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\models;

use yii\helpers\ArrayHelper;
use backendApiRbacV1\behaviors\QueryJoinWithBehaviorInterface;
use backendApi\behaviors\VirtualAttributesBehavior;

/**
 * 查询客户端数据模型。
 * 
 * @property int $user_id 用户ID
 * @property string $user_account 用户标识
 * @property string $user_description 用户描述
 * @property int $user_create_time 用户创建时间
 * 
 * @property int $role_id 角色ID
 * @property string $role_name 角色名称
 * @property string $role_description 角色描述
 * @property int $role_create_time 角色创建时间
 * @property int $role_status 角色状态
 * 
 * @property int $permission_id 权限ID
 * @property string $permission_name 权限名称
 * @property string $permission_description 权限描述
 * @property int $permission_create_time 权限创建时间
 * @property int $permission_status 权限状态
 * 
 * @property int $operation_id 操作ID
 * @property string $operation_code 操作编码
 * @property string $operation_name 操作名称
 * @property string $operation_description 操作描述
 * @property int $operation_create_time 操作创建时间
 * @property int $operation_status 操作状态
 * @property string $operation_data 操作额外数据
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacClientSearch extends RbacClient implements QueryJoinWithBehaviorInterface
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        // 获取全部查询属性。
        $searchAttributes = array_keys($this->searchAttributeFieldMap());
        
        // 移除自身已存在的属性。
        $virtualAttributes = array_diff($searchAttributes, $this->attributes());
        
        return ArrayHelper::merge([
            // 为模型添加虚拟属性的行为。
            'virtualAttributesBehavior' => [
                'class' => VirtualAttributesBehavior::class,
                'attributes' => $virtualAttributes,
            ],
        ], parent::behaviors());
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // self
            [['id', 'create_time'], 'integer'],
            [['identifier', 'name', 'description', 'type'], 'string'],
            // user
            [['user_id', 'user_create_time'], 'integer'],
            [['user_account', 'user_description'], 'string'],
            // role
            [['role_id', 'role_create_time', 'role_status'], 'integer'],
            [['role_name', 'role_description'], 'string'],
            // permission
            [['permission_id', 'permission_create_time', 'permission_status'], 'integer'],
            [['permission_name', 'permission_description'], 'string'],
            // operation
            [['operation_id', 'operation_create_time', 'operation_status'], 'integer'],
            [['operation_code', 'operation_name', 'operation_description', 'operation_data'], 'string'],
        ];
    }
    
    /**
     * 查询属性和字段的映射。
     * 
     * @return array
     */
    public function searchAttributeFieldMap()
    {
        return [
            // self
            'id' => static::tableName() . '.id',
            'identifier' => static::tableName() . '.identifier',
            'name' => static::tableName() . '.name',
            'description' => static::tableName() . '.description',
            'type' => static::tableName() . '.type',
            'create_time' => static::tableName() . '.create_time',
            // user
            'user_id' => RbacUserSearch::tableName() . '.id',
            'user_account' => RbacUserSearch::tableName() . '.account',
            'user_description' => RbacUserSearch::tableName() . '.description',
            'user_create_time' => RbacUserSearch::tableName() . '.create_time',
            // role
            'role_id' => RbacRoleSearch::tableName() . '.id',
            'role_name' => RbacRoleSearch::tableName() . '.name',
            'role_description' => RbacRoleSearch::tableName() . '.description',
            'role_create_time' => RbacRoleSearch::tableName() . '.create_time',
            'role_status' => RbacRoleSearch::tableName() . '.status',
            // permission
            'permission_id' => RbacPermissionSearch::tableName() . '.id',
            'permission_name' => RbacPermissionSearch::tableName() . '.name',
            'permission_description' => RbacPermissionSearch::tableName() . '.description',
            'permission_create_time' => RbacPermissionSearch::tableName() . '.create_time',
            'permission_status' => RbacPermissionSearch::tableName() . '.status',
            // operation
            'operation_id' => RbacOperationSearch::tableName() . '.id',
            'operation_code' => RbacOperationSearch::tableName() . '.code',
            'operation_name' => RbacOperationSearch::tableName() . '.name',
            'operation_description' => RbacOperationSearch::tableName() . '.description',
            'operation_create_time' => RbacOperationSearch::tableName() . '.create_time',
            'operation_status' => RbacOperationSearch::tableName() . '.status',
            'operation_data' => RbacOperationSearch::tableName() . '.data',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getQueryJoinWithByTables($names, $query)
    {
        $with = [];
        foreach ($names as $name) {
            switch ($name) {
                case RbacUserSearch::tableName():
                    $with[] = 'rbacUsers';
                    break;
                case RbacRoleSearch::tableName():
                    $with[] = 'rbacRoles';
                    break;
                case RbacPermissionSearch::tableName():
                    $with[] = 'rbacPermissions';
                    break;
                case RbacOperationSearch::tableName():
                    $with[] = 'rbacOperations';
                    break;
            }
        }
        
        return $with;
    }
}
