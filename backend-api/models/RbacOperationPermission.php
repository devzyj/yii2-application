<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
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
 * @property RbacPermission $permission
 * @property RbacOperation $operation
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
    public function rules()
    {
        return [
            [['operation_id', 'permission_id', 'create_time'], 'required'],
            [['operation_id', 'permission_id', 'create_time'], 'integer'],
            [['operation_id', 'permission_id'], 'unique', 'targetAttribute' => ['operation_id', 'permission_id']],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacPermission::className(), 'targetAttribute' => ['permission_id' => 'id']],
            [['operation_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacOperation::className(), 'targetAttribute' => ['operation_id' => 'id']],
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
     * @return \yii\db\ActiveQuery
     */
    public function getPermission()
    {
        return $this->hasOne(RbacPermission::className(), ['id' => 'permission_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperation()
    {
        return $this->hasOne(RbacOperation::className(), ['id' => 'operation_id']);
    }
}
