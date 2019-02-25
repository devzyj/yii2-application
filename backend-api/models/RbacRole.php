<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_role}}".
 *
 * @property int $id ID
 * @property int $client_id 客户端 ID
 * @property string $name 名称
 * @property string $description 描述
 * @property int $create_time 创建时间
 * @property int $status 状态（0=禁用；1=可用）
 *
 * @property RbacPermissionRole[] $rbacPermissionRoles
 * @property RbacPermission[] $permissions
 * @property RbacClient $client
 * @property RbacRoleUser[] $rbacRoleUsers
 * @property RbacUser[] $users
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_role}}';
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
            [['client_id', 'create_time', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['client_id', 'name'], 'unique', 'targetAttribute' => ['client_id', 'name']],
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
            'name' => 'Name',
            'description' => 'Description',
            'create_time' => 'Create Time',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissionRoles()
    {
        return $this->hasMany(RbacPermissionRole::className(), ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(RbacPermission::className(), ['id' => 'permission_id'])->viaTable('{{%rbac_permission_role}}', ['role_id' => 'id']);
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
    public function getRbacRoleUsers()
    {
        return $this->hasMany(RbacRoleUser::className(), ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(RbacUser::className(), ['id' => 'user_id'])->viaTable('{{%rbac_role_user}}', ['role_id' => 'id']);
    }
}
