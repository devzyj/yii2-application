<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_operation}}".
 *
 * @property Client $client 客户端
 * @property OperationPermission[] $operationPermissions 操作与权限关联
 * @property Permission[] $permissions 权限
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Operation extends \common\models\rbac\Operation
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
            'client_id', 'code', 'name', 'description', 'status'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'code', 'name', 'description', 'status'
        ];
    
        // 返回修改后的场景列表。
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function extraFields()
    {
        return ['client', 'permissions'];
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
    public function getOperationPermissions()
    {
        return $this->hasMany(OperationPermission::class, ['operation_id' => 'id']);
    }

    /**
     * 权限查询对像。
     * 
     * @return PermissionQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::class, ['id' => 'permission_id'])->viaTable(OperationPermission::tableName(), ['operation_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return OperationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OperationQuery(get_called_class());
    }
}
