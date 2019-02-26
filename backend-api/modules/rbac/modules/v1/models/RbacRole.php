<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\models;

use Yii;
use backendApi\models\RbacPermissionRole;
use backendApi\models\RbacRoleUser;

/**
 * This is the model class for table "{{%rbac_role}}".
 *
 * @property RbacPermission[] $rbacPermissions 权限
 * @property RbacClient $rbacClient 客户端
 * @property RbacUser[] $rbacUsers 用户
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacRole extends \backendApi\models\RbacRole
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
     * 获取权限查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissions()
    {
        return $this->hasMany(RbacPermission::class, ['id' => 'permission_id'])->viaTable(RbacPermissionRole::tableName(), ['role_id' => 'id']);
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
     * 获取用户查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacUsers()
    {
        return $this->hasMany(RbacUser::class, ['id' => 'user_id'])->viaTable(RbacRoleUser::tableName(), ['role_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return RbacRoleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RbacRoleQuery(get_called_class());
    }
}
