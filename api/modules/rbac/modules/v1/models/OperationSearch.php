<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use yii\helpers\ArrayHelper;
use apiRbacV1\components\behaviors\QueryJoinWithBehaviorInterface;

/**
 * 查询操作数据模型。
 * 
 * @property int $client_id 客户端ID
 * @property string $client_name 客户端名称
 * @property string $client_identifier API客户端标识
 * @property string $client_description 客户端描述
 * @property string $client_type 客户端类型
 * @property int $client_create_time 客户端创建时间
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
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OperationSearch extends Operation implements QueryJoinWithBehaviorInterface
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
            [['id', 'client_id', 'create_time', 'status'], 'integer'],
            [['code', 'name', 'description', 'data'], 'string'],
            // client
            [['client_create_time'], 'integer'],
            [['client_name', 'client_identifier', 'client_description'], 'string'],
            // permission
            [['permission_id', 'permission_create_time', 'permission_status'], 'integer'],
            [['permission_name', 'permission_description'], 'string'],
            // role
            [['role_id', 'role_create_time', 'role_status'], 'integer'],
            [['role_name', 'role_description'], 'string'],
            // user
            [['user_id', 'user_create_time'], 'integer'],
            [['user_account', 'user_description'], 'string'],
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
            'client_id' => static::tableName() . '.client_id',
            'code' => static::tableName() . '.code',
            'name' => static::tableName() . '.name',
            'description' => static::tableName() . '.description',
            'create_time' => static::tableName() . '.create_time',
            'status' => static::tableName() . '.status',
            'data' => static::tableName() . '.data',
            // client
            'client_name' => ClientSearch::tableName() . '.name',
            'client_identifier' => ClientSearch::tableName() . '.identifier',
            'client_description' => ClientSearch::tableName() . '.description',
            'client_type' => ClientSearch::tableName() . '.type',
            'client_create_time' => ClientSearch::tableName() . '.create_time',
            // permission
            'permission_id' => PermissionSearch::tableName() . '.id',
            'permission_name' => PermissionSearch::tableName() . '.name',
            'permission_description' => PermissionSearch::tableName() . '.description',
            'permission_create_time' => PermissionSearch::tableName() . '.create_time',
            'permission_status' => PermissionSearch::tableName() . '.status',
            // role
            'role_id' => RoleSearch::tableName() . '.id',
            'role_name' => RoleSearch::tableName() . '.name',
            'role_description' => RoleSearch::tableName() . '.description',
            'role_create_time' => RoleSearch::tableName() . '.create_time',
            'role_status' => RoleSearch::tableName() . '.status',
            // user
            'user_id' => UserSearch::tableName() . '.id',
            'user_account' => UserSearch::tableName() . '.account',
            'user_description' => UserSearch::tableName() . '.description',
            'user_create_time' => UserSearch::tableName() . '.create_time',
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
                case ClientSearch::tableName():
                    $with[] = 'client';
                    break;
                case PermissionSearch::tableName():
                    $with[] = 'permissions';
                    break;
                case RoleSearch::tableName():
                    $with[] = 'permissions.roles';
                    break;
                case UserSearch::tableName():
                    $with[] = 'permissions.roles.users';
                    break;
            }
        }
        
        return $with;
    }
}
