<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_client}}".
 *
 * @property RbacOperation[] $rbacOperations 操作
 * @property RbacPermission[] $rbacPermissions 权限
 * @property RbacRole[] $rbacRoles 角色
 * @property RbacUser[] $rbacUsers 用户
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacClient extends \backendApi\models\RbacClient
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
            'identifier', 'name', 'description', 'type'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'identifier', 'name', 'description', 'type'
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
        return $this->hasMany(RbacOperation::class, ['client_id' => 'id']);
    }

    /**
     * 获取权限查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissions()
    {
        return $this->hasMany(RbacPermission::class, ['client_id' => 'id']);
    }

    /**
     * 获取角色查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoles()
    {
        return $this->hasMany(RbacRole::class, ['client_id' => 'id']);
    }

    /**
     * 获取用户查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacUsers()
    {
        return $this->hasMany(RbacUser::class, ['client_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return RbacClientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RbacClientQuery(get_called_class());
    }
}
