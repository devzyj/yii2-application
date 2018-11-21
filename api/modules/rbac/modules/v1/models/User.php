<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_user}}".
 *
 * @property Client $client 客户端
 * @property RoleUser[] $roleUsers  角色与用户关联
 * @property Role[] $roles 角色
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class User extends \common\models\rbac\User
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
            'client_id', 'account', 'description'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'account', 'description'
        ];
    
        // 返回修改后的场景列表。
        return $scenarios;
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
    public function getRoleUsers()
    {
        return $this->hasMany(RoleUser::class, ['user_id' => 'id']);
    }

    /**
     * 角色查询对像。
     * 
     * @return RoleQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->viaTable(RoleUser::tableName(), ['user_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }
}
