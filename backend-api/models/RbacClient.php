<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_client}}".
 *
 * @property int $id ID
 * @property string $identifier 授权客户端标识
 * @property string $name 名称
 * @property string $description 描述
 * @property string $type 类型
 * @property int $create_time 创建时间
 *
 * @property RbacGroup[] $rbacGroups
 * @property RbacMenu[] $rbacMenus
 * @property RbacOperation[] $rbacOperations
 * @property RbacPermission[] $rbacPermissions
 * @property RbacRole[] $rbacRoles
 * @property RbacUser[] $rbacUsers
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacClient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_client}}';
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
            [['identifier', 'name', 'type', 'create_time'], 'required'],
            [['create_time'], 'integer'],
            [['identifier', 'type'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['identifier'], 'unique'],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identifier' => 'Identifier',
            'name' => 'Name',
            'description' => 'Description',
            'type' => 'Type',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacGroups()
    {
        return $this->hasMany(RbacGroup::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacMenus()
    {
        return $this->hasMany(RbacMenu::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacOperations()
    {
        return $this->hasMany(RbacOperation::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissions()
    {
        return $this->hasMany(RbacPermission::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoles()
    {
        return $this->hasMany(RbacRole::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacUsers()
    {
        return $this->hasMany(RbacUser::className(), ['client_id' => 'id']);
    }
}
