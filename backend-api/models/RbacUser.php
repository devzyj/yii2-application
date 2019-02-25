<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_user}}".
 *
 * @property int $id ID
 * @property int $client_id 客户端 ID
 * @property string $identifier 用户标识
 * @property string $description 描述
 * @property int $create_time 创建时间
 *
 * @property RbacGroupUser[] $rbacGroupUsers
 * @property RbacGroup[] $groups
 * @property RbacRoleUser[] $rbacRoleUsers
 * @property RbacRole[] $roles
 * @property RbacClient $client
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_user}}';
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
            [['client_id', 'identifier', 'create_time'], 'required'],
            [['client_id', 'create_time'], 'integer'],
            [['identifier', 'description'], 'string', 'max' => 255],
            [['client_id', 'identifier'], 'unique', 'targetAttribute' => ['client_id', 'identifier']],
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
            'identifier' => 'Identifier',
            'description' => 'Description',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacGroupUsers()
    {
        return $this->hasMany(RbacGroupUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(RbacGroup::className(), ['id' => 'group_id'])->viaTable('{{%rbac_group_user}}', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoleUsers()
    {
        return $this->hasMany(RbacRoleUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(RbacRole::className(), ['id' => 'role_id'])->viaTable('{{%rbac_role_user}}', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(RbacClient::className(), ['id' => 'client_id']);
    }
}
