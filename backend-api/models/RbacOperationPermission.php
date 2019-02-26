<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_operation_permission}}".
 *
 * @property int $operation_id 操作 ID
 * @property int $permission_id 权限 ID
 * @property int $create_time 创建时间
 *
 * @property RbacPermission $rbacPermission 权限
 * @property RbacOperation $rbacOperation 操作
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacOperationPermission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_operation_permission}}';
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
            [['operation_id', 'permission_id'], 'required'],
            [['operation_id', 'permission_id'], 'integer'],
            [['operation_id', 'permission_id'], 'unique', 'targetAttribute' => ['operation_id', 'permission_id']],
            [['operation_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacOperation::class, 'targetAttribute' => ['operation_id' => 'id']],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacPermission::class, 'targetAttribute' => ['permission_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'operation_id' => 'Operation ID',
            'permission_id' => 'Permission ID',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * 获取权限查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermission()
    {
        return $this->hasOne(RbacPermission::class, ['id' => 'permission_id']);
    }

    /**
     * 获取操作查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacOperation()
    {
        return $this->hasOne(RbacOperation::class, ['id' => 'operation_id']);
    }
}
