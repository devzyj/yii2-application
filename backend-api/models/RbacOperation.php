<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;
use backendApi\validators\rbac\OperationCodeValidator;

/**
 * This is the model class for table "{{%rbac_operation}}".
 *
 * @property int $id ID
 * @property int $client_id 客户端 ID
 * @property string $code 编码
 * @property string $name 名称
 * @property string $description 描述
 * @property int $create_time 创建时间
 * @property int $status 状态（0=禁用；1=可用）
 * @property string $data 额外数据
 *
 * @property RbacClient $rbacClient 客户端
 * @property RbacOperationPermission[] $rbacOperationPermissions 操作与权限关联数据
 * @property RbacPermission[] $rbacPermissions 权限
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacOperation extends \yii\db\ActiveRecord
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
            // 过滤和处理数据。
            [['code'], 'filter', 'filter' => 'strtolower'],
            [['data'], 'default', 'value' => ''],
            // 验证规则。
            [['client_id', 'code', 'name'], 'required'],
            [['client_id'], 'integer'],
            [['code'], OperationCodeValidator::class],
            [['code', 'description'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 50],
            [['data'], 'string', 'max' => 5000],
            [['status'], 'boolean'],
            [['code'], 'unique', 'targetAttribute' => ['client_id', 'code']],
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
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'create_time' => 'Create Time',
            'status' => 'Status',
            'data' => 'Data',
        ];
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
     * 获取操作与权限关联查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacOperationPermissions()
    {
        return $this->hasMany(RbacOperationPermission::class, ['operation_id' => 'id']);
    }

    /**
     * 获取权限查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissions()
    {
        return $this->hasMany(RbacPermission::class, ['id' => 'permission_id'])->viaTable(RbacOperationPermission::tableName(), ['operation_id' => 'id']);
    }
}
