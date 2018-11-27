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
 * 查询角色数据模型。
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
class RoleSearch extends Role implements QueryJoinWithBehaviorInterface
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
            [['name', 'description'], 'string'],
            // client
            [['client_create_time'], 'integer'],
            [['client_name', 'client_identifier', 'client_description'], 'string'],
            // user
            [['user_id', 'user_create_time'], 'integer'],
            [['user_account', 'user_description'], 'string'],
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
            'client_id' => static::tableName() . '.client_id',
            'name' => static::tableName() . '.name',
            'description' => static::tableName() . '.description',
            'create_time' => static::tableName() . '.create_time',
            'status' => static::tableName() . '.status',
            // client
            'client_name' => ClientSearch::tableName() . '.name',
            'client_identifier' => ClientSearch::tableName() . '.identifier',
            'client_description' => ClientSearch::tableName() . '.description',
            'client_type' => ClientSearch::tableName() . '.type',
            'client_create_time' => ClientSearch::tableName() . '.create_time',
            // user
            'user_id' => UserSearch::tableName() . '.id',
            'user_account' => UserSearch::tableName() . '.account',
            'user_description' => UserSearch::tableName() . '.description',
            'user_create_time' => UserSearch::tableName() . '.create_time',
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
                case ClientSearch::tableName():
                    $with[] = 'client';
                    break;
                case UserSearch::tableName():
                    $with[] = 'users';
                    break;
                case PermissionSearch::tableName():
                    $with[] = 'permissions';
                    break;
                case OperationSearch::tableName():
                    $with[] = 'permissions.operations';
                    break;
            }
        }
        
        return $with;
    }
}