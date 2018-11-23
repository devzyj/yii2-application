<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use yii\helpers\ArrayHelper;
use apiRbacV1\components\behaviors\QueryJoinWithBehaviorInterface;

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
class ClientSearch extends Client implements QueryJoinWithBehaviorInterface
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $searchAttributes = array_keys($this->searchAttributeFieldMap());
        $virtualAttributes = array_diff($searchAttributes, $this->attributes());
        
        return ArrayHelper::merge([
            // 为模型添加虚拟属性的行为。
            'virtualAttributesBehavior' => [
                'class' => 'common\behaviors\VirtualAttributesBehavior',
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
            [['name', 'identifier', 'description', 'type'], 'string'],
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
            'name' => static::tableName() . '.name',
            'identifier' => static::tableName() . '.identifier',
            'description' => static::tableName() . '.description',
            'type' => static::tableName() . '.type',
            'create_time' => static::tableName() . '.create_time',
            // user
            'user_id' => UserSearch::tableName() . '.id',
            'user_account' => UserSearch::tableName() . '.account',
            'user_description' => UserSearch::tableName() . '.description',
            'user_create_time' => UserSearch::tableName() . '.create_time',
            // role
            'role_id' => RoleSearch::tableName() . '.id',
            'role_name' => RoleSearch::tableName() . '.name',
            'role_description' => RoleSearch::tableName() . '.description',
            'role_create_time' => RoleSearch::tableName() . '.create_time',
            'role_status' => RoleSearch::tableName() . '.status',
            // permission
            'permission_id' => PermissionSearch::tableName() . '.id',
            'permission_name' => PermissionSearch::tableName() . '.name',
            'permission_description' => PermissionSearch::tableName() . '.description',
            'permission_create_time' => PermissionSearch::tableName() . '.create_time',
            'permission_status' => PermissionSearch::tableName() . '.status',
            // operation
            'operation_id' => OperationSearch::tableName() . '.id',
            'operation_code' => OperationSearch::tableName() . '.code',
            'operation_name' => OperationSearch::tableName() . '.name',
            'operation_description' => OperationSearch::tableName() . '.description',
            'operation_create_time' => OperationSearch::tableName() . '.create_time',
            'operation_status' => OperationSearch::tableName() . '.status',
            'operation_data' => OperationSearch::tableName() . '.data',
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
                case UserSearch::tableName():
                    $with[] = 'users';
                    break;
                case RoleSearch::tableName():
                    $with[] = 'roles';
                    break;
                case PermissionSearch::tableName():
                    $with[] = 'permissions';
                    break;
                case OperationSearch::tableName():
                    $with[] = 'operations';
                    break;
            }
        }
        
        return $with;
    }
}
