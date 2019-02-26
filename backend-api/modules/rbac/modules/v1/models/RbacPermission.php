<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\models;

use Yii;
use backendApi\models\RbacOperationPermission;
use backendApi\models\RbacPermissionRole;

/**
 * This is the model class for table "{{%rbac_permission}}".
 *
 * @property RbacOperation[] $rbacOperations 操作
 * @property RbacClient $rbacClient 客户端
 * @property RbacRole[] $rbacRoles 角色
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacPermission extends \backendApi\models\RbacPermission
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
     * 获取操作查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacOperations()
    {
        return $this->hasMany(RbacOperation::class, ['id' => 'operation_id'])->viaTable(RbacOperationPermission::tableName(), ['permission_id' => 'id']);
    }

    /**
     * 获取客户端查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacClient()
    {
        return $this->hasOne(RbacClient::class, ['id' => 'client_id']);
    }

    /**
     * 获取角色查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoles()
    {
        return $this->hasMany(RbacRole::class, ['id' => 'role_id'])->viaTable(RbacPermissionRole::tableName(), ['permission_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return RbacPermissionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RbacPermissionQuery(get_called_class());
    }
}
