<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\rbac;

use Yii;

/**
 * This is the model class for table "{{%rbac_permission}}".
 *
 * @property int $id 权限ID
 * @property int $client_id 客户端ID
 * @property string $name 权限名称
 * @property string $description 权限描述
 * @property int $create_time 创建时间
 * @property int $status 权限状态（0=禁用；1=可用）
 *
 * @property Client $client 客户端
 * @property PermissionRole[] $permissionRoles 权限与角色关联
 * @property Role[] $roles 角色
 * @property OperationPermission[] $operationPermissions 操作与权限关联
 * @property Operation[] $operations 操作
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Permission extends \yii\db\ActiveRecord
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
            // 过滤和处理数据。
            [['name'], 'trim'],
            [['description'], 'default', 'value' => ''],
            [['status'], 'default', 'value' => self::STATUS_DISABLED],
            // 验证规则。
            [['client_id', 'name'], 'required'],
            [['client_id'], 'integer', 'integerOnly' => true],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['status'], 'boolean'],
            [['name'], 'unique', 'targetAttribute' => ['client_id', 'name']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'create_time' => Yii::t('app', 'Create Time'),
            'status' => Yii::t('app', 'Status'),
        ];
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
    public function getPermissionRoles()
    {
        return $this->hasMany(PermissionRole::class, ['permission_id' => 'id']);
    }

    /**
     * 角色查询对像。
     * 
     * @return RoleQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->viaTable(PermissionRole::tableName(), ['permission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperationPermissions()
    {
        return $this->hasMany(OperationPermission::class, ['permission_id' => 'id']);
    }

    /**
     * 操作查询对像。
     * 
     * @return PermissionQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operation::class, ['id' => 'operation_id'])->viaTable(OperationPermission::tableName(), ['permission_id' => 'id']);
    }
}
