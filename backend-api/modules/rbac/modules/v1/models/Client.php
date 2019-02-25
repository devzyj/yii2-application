<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_client}}".
 *
 * @property User[] $users 用户
 * @property Role[] $roles 角色
 * @property Permission[] $permissions 权限
 * @property Operation[] $operations 操作
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Client extends \common\models\rbac\Client
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
            'name', 'identifier', 'description', 'type'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'name', 'identifier', 'description', 'type'
        ];
    
        // 返回修改后的场景列表。
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function extraFields()
    {
        return ['users', 'roles', 'permissions', 'operations'];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operation::class, ['client_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return ClientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientQuery(get_called_class());
    }
}
