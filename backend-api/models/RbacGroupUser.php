<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_group_user}}".
 *
 * @property int $group_id 组 ID
 * @property int $user_id 用户 ID
 * @property int $is_admin 是否管理员（0=否；1=是）
 * @property int $create_time 创建时间
 *
 * @property RbacUser $user
 * @property RbacGroup $group
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacGroupUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_group_user}}';
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
            [['group_id', 'user_id', 'create_time'], 'required'],
            [['group_id', 'user_id', 'is_admin', 'create_time'], 'integer'],
            [['group_id', 'user_id'], 'unique', 'targetAttribute' => ['group_id', 'user_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacUser::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Group ID',
            'user_id' => 'User ID',
            'is_admin' => 'Is Admin',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(RbacUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(RbacGroup::className(), ['id' => 'group_id']);
    }
}
