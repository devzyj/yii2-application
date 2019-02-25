<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_permission_group}}".
 *
 * @property int $permission_id 权限 ID
 * @property int $group_id 组 ID
 * @property int $create_time 创建时间
 *
 * @property RbacGroup $group
 * @property RbacPermission $permission
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacPermissionGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_permission_group}}';
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
            [['permission_id', 'group_id', 'create_time'], 'required'],
            [['permission_id', 'group_id', 'create_time'], 'integer'],
            [['permission_id', 'group_id'], 'unique', 'targetAttribute' => ['permission_id', 'group_id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['permission_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacPermission::className(), 'targetAttribute' => ['permission_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permission_id' => 'Permission ID',
            'group_id' => 'Group ID',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(RbacGroup::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermission()
    {
        return $this->hasOne(RbacPermission::className(), ['id' => 'permission_id']);
    }
}
