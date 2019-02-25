<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_permission_role}}".
 *
 * @property int $permission_id 权限 ID
 * @property int $role_id 角色 ID
 * @property int $create_time 创建时间
 *
 * @property RbacRole $role
 * @property RbacPermission $permission
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacPermissionRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_permission_role}}';
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
            [['permission_id', 'role_id', 'create_time'], 'required'],
            [['permission_id', 'role_id', 'create_time'], 'integer'],
            [['permission_id', 'role_id'], 'unique', 'targetAttribute' => ['permission_id', 'role_id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacRole::className(), 'targetAttribute' => ['role_id' => 'id']],
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
            'role_id' => 'Role ID',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(RbacRole::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermission()
    {
        return $this->hasOne(RbacPermission::className(), ['id' => 'permission_id']);
    }
}
