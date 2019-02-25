<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

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
 * @property RbacClient $client
 * @property RbacOperationPermission[] $rbacOperationPermissions
 * @property RbacPermission[] $permissions
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacOperation extends \yii\db\ActiveRecord
{
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
    public function rules()
    {
        return [
            [['client_id', 'code', 'name', 'create_time', 'data'], 'required'],
            [['client_id', 'create_time', 'status'], 'integer'],
            [['data'], 'string'],
            [['code', 'description'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 50],
            [['client_id', 'code'], 'unique', 'targetAttribute' => ['client_id', 'code']],
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
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'create_time' => 'Create Time',
            'status' => 'Status',
            'data' => 'Data',
        ];
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
    public function getRbacOperationPermissions()
    {
        return $this->hasMany(RbacOperationPermission::className(), ['operation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(RbacPermission::className(), ['id' => 'permission_id'])->viaTable('{{%rbac_operation_permission}}', ['operation_id' => 'id']);
    }
}
