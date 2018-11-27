<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\rbac;

use Yii;

/**
 * This is the model class for table "{{%rbac_operation}}".
 *
 * @property int $id 操作ID
 * @property int $client_id 客户端ID
 * @property string $code 操作编码
 * @property string $name 操作名称
 * @property string $description 操作描述
 * @property int $create_time 创建时间
 * @property int $status 操作状态（0=禁用；1=可用）
 * @property string $data 额外数据
 *
 * @property Client $client 客户端
 * @property OperationPermission[] $operationPermissions 操作与权限关联
 * @property Permission[] $permissions 权限
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Operation extends \yii\db\ActiveRecord
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
        return '{{%rbac_operation}}';
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
            [['code', 'name'], 'trim'],
            [['code'], 'filter', 'filter' => 'strtolower'],
            [['description', 'data'], 'default', 'value' => ''],
            [['status'], 'default', 'value' => self::STATUS_DISABLED],
            // 验证规则。
            [['client_id', 'code', 'name'], 'required'],
            [['client_id'], 'integer', 'integerOnly' => true],
            [['code'], 'common\validators\rbac\OperationCodeValidator'],
            [['code', 'description'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 50],
            [['data'], 'string', 'max' => 5000],
            [['status'], 'boolean'],
            [['code'], 'unique', 'targetAttribute' => ['client_id', 'code']],
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
            'id' => 'ID',
            'client_id' => 'Client ID',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'create_time' => 'Create Time',
            'status' => 'Status',
            'data' => 'Data',
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
    public function getOperationPermissions()
    {
        return $this->hasMany(OperationPermission::class, ['operation_id' => 'id']);
    }

    /**
     * 权限查询对像。
     * 
     * @return PermissionQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::class, ['id' => 'permission_id'])->viaTable(OperationPermission::tableName(), ['operation_id' => 'id']);
    }
}