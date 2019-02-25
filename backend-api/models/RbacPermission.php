<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_permission}}".
 *
 * @property int $id ID
 * @property int $client_id 客户端 ID
 * @property string $name 名称
 * @property string $description 描述
 * @property int $create_time 创建时间
 * @property int $status 状态（0=禁用；1=可用）
 *
 * @property RbacMenuPermission[] $rbacMenuPermissions
 * @property RbacMenu[] $menus
 * @property RbacOperationPermission[] $rbacOperationPermissions
 * @property RbacOperation[] $operations
 * @property RbacClient $client
 * @property RbacPermissionGroup[] $rbacPermissionGroups
 * @property RbacGroup[] $groups
 * @property RbacPermissionRole[] $rbacPermissionRoles
 * @property RbacRole[] $roles
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacPermission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_permission}}';
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
    public function getRbacMenuPermissions()
    {
        return $this->hasMany(RbacMenuPermission::className(), ['permission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(RbacMenu::className(), ['id' => 'menu_id'])->viaTable('{{%rbac_menu_permission}}', ['permission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacOperationPermissions()
    {
        return $this->hasMany(RbacOperationPermission::className(), ['permission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(RbacOperation::className(), ['id' => 'operation_id'])->viaTable('{{%rbac_operation_permission}}', ['permission_id' => 'id']);
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
    public function getRbacPermissionGroups()
    {
        return $this->hasMany(RbacPermissionGroup::className(), ['permission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(RbacGroup::className(), ['id' => 'group_id'])->viaTable('{{%rbac_permission_group}}', ['permission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissionRoles()
    {
        return $this->hasMany(RbacPermissionRole::className(), ['permission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(RbacRole::className(), ['id' => 'role_id'])->viaTable('{{%rbac_permission_role}}', ['permission_id' => 'id']);
    }
}
