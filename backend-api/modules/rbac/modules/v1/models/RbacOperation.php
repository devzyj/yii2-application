<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\models;

use Yii;
use backendApi\models\RbacOperationPermission;

/**
 * This is the model class for table "{{%rbac_operation}}".
 *
 * @property RbacClient $rbacClient 客户端
 * @property RbacPermission[] $rbacPermissions 权限
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacOperation extends \backendApi\models\RbacOperation
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
            'client_id', 'code', 'name', 'description', 'status', 'data'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'code', 'name', 'description', 'status', 'data'
        ];
    
        // 返回修改后的场景列表。
        return $scenarios;
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
     * 获取权限查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissions()
    {
        return $this->hasMany(RbacPermission::class, ['id' => 'permission_id'])->viaTable(RbacOperationPermission::tableName(), ['operation_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return RbacOperationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RbacOperationQuery(get_called_class());
    }
}
