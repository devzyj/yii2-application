<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\rbac;

use Yii;

/**
 * This is the model class for table "{{%rbac_operation_permission}}".
 *
 * @property int $operation_id 操作ID
 * @property int $permission_id 权限ID
 * @property int $create_time 创建时间
 *
 * @property Operation $operation 操作
 * @property Permission $permission 权限
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OperationPermission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_operation_permission}}';
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
            [['operation_id', 'permission_id'], 'integer', 'integerOnly' => true],
            [['operation_id', 'permission_id'], 'unique', 'targetAttribute' => ['operation_id', 'permission_id']],
            [['operation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Operation::className(), 'targetAttribute' => ['operation_id' => 'id']],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => Permission::className(), 'targetAttribute' => ['permission_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'operation_id' => Yii::t('app', 'Operation ID'),
            'permission_id' => Yii::t('app', 'Permission ID'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }

    /**
     * 操作查询对像。
     * 
     * @return OperationQuery
     */
    public function getOperation()
    {
        return $this->hasOne(Operation::className(), ['id' => 'operation_id']);
    }

    /**
     * 权限查询对像。
     * 
     * @return PermissionQuery
     */
    public function getPermission()
    {
        return $this->hasOne(Permission::className(), ['id' => 'permission_id']);
    }
}
