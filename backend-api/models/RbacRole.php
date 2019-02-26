<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
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
 * @property RbacPermissionRole[] $rbacPermissionRoles 权限与角色关联数据
 * @property RbacPermission[] $rbacPermissions 权限
 * @property RbacClient $rbacClient 客户端
 * @property RbacRoleUser[] $rbacRoleUsers 角色与用户关联数据
 * @property RbacUser[] $rbacUsers 用户
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacRole extends \yii\db\ActiveRecord
{
    /**
     * @var integer 状态 - 禁用的。
     */
    const STATUS_DISABLED = 0;

    /**
     * @var integer 状态 - 启用的。
     */
    const STATUS_ENABLED = 1;
    
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
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => null,
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'name'], 'required'],
            [['client_id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['status'], 'boolean'],
            [['name'], 'unique', 'targetAttribute' => ['client_id', 'name']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacClient::class, 'targetAttribute' => ['client_id' => 'id']],
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
     * 获取权限与角色关联查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissionRoles()
    {
        return $this->hasMany(RbacPermissionRole::class, ['role_id' => 'id']);
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
     * 获取角色与用户关联查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoleUsers()
    {
        return $this->hasMany(RbacRoleUser::class, ['role_id' => 'id']);
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
}
