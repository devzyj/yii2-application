<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_group}}".
 *
 * @property int $id ID
 * @property int $client_id 客户端 ID
 * @property int $parent_id 上级 ID
 * @property string $name 名称
 * @property int $create_time 创建时间
 *
 * @property RbacGroup $parent
 * @property RbacGroup[] $rbacGroups
 * @property RbacClient $client
 * @property RbacGroupUser[] $rbacGroupUsers
 * @property RbacUser[] $users
 * @property RbacPermissionGroup[] $rbacPermissionGroups
 * @property RbacPermission[] $permissions
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_group}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_backend');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'name', 'create_time'], 'required'],
            [['client_id', 'parent_id', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['client_id', 'parent_id', 'name'], 'unique', 'targetAttribute' => ['client_id', 'parent_id', 'name']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacGroup::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacClient::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(RbacGroup::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacGroups()
    {
        return $this->hasMany(RbacGroup::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(RbacClient::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacGroupUsers()
    {
        return $this->hasMany(RbacGroupUser::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(RbacUser::className(), ['id' => 'user_id'])->viaTable('{{%rbac_group_user}}', ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissionGroups()
    {
        return $this->hasMany(RbacPermissionGroup::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(RbacPermission::className(), ['id' => 'permission_id'])->viaTable('{{%rbac_permission_group}}', ['group_id' => 'id']);
    }
}
