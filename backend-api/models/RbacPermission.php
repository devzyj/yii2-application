<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
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
 * @property RbacOperationPermission[] $rbacOperationPermissions 操作与权限关联数据
 * @property RbacOperation[] $rbacOperations 操作
 * @property RbacClient $rbacClient 客户端
 * @property RbacPermissionRole[] $rbacPermissionRoles 权限与角色关联数据
 * @property RbacRole[] $rbacRoles 角色
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacPermission extends \yii\db\ActiveRecord
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
     * 获取操作与权限关联查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacOperationPermissions()
    {
        return $this->hasMany(RbacOperationPermission::class, ['permission_id' => 'id']);
    }

    /**
     * 获取操作查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacOperations()
    {
        return $this->hasMany(RbacOperation::class, ['id' => 'operation_id'])->viaTable(RbacOperationPermission::tableName(), ['permission_id' => 'id']);
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
     * 获取权限与角色关联查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissionRoles()
    {
        return $this->hasMany(RbacPermissionRole::class, ['permission_id' => 'id']);
    }

    /**
     * 获取角色查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoles()
    {
        return $this->hasMany(RbacRole::class, ['id' => 'role_id'])->viaTable(RbacPermissionRole::tableName(), ['permission_id' => 'id']);
    }
}
