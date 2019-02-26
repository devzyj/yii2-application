<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\models;

use Yii;
use backendApi\models\RbacRoleUser;

/**
 * This is the model class for table "{{%rbac_user}}".
 *
 * @property RbacRole[] $rbacRoles 角色
 * @property RbacClient $rbacClient 客户端
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacUser extends \backendApi\models\RbacUser
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
            'client_id', 'identifier', 'description'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'identifier', 'description'
        ];
    
        // 返回修改后的场景列表。
        return $scenarios;
    }

    /**
     * 获取角色查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoles()
    {
        return $this->hasMany(RbacRole::class, ['id' => 'role_id'])->viaTable(RbacRoleUser::tableName(), ['user_id' => 'id']);
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
     * {@inheritdoc}
     * 
     * @return RbacUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RbacUserQuery(get_called_class());
    }
}
