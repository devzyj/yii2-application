<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%rbac_menu}}".
 *
 * @property int $id ID
 * @property int $client_id 客户端 ID
 * @property int $parent_id 上级 ID
 * @property string $name 名称
 * @property string $description 描述
 * @property int $order 排序
 * @property int $create_time 创建时间
 * @property int $status 状态（0=禁用；1=可用）
 * @property string $data 额外数据
 *
 * @property RbacMenu $parent
 * @property RbacMenu[] $rbacMenus
 * @property RbacClient $client
 * @property RbacMenuPermission[] $rbacMenuPermissions
 * @property RbacPermission[] $permissions
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacMenu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_menu}}';
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
            [['client_id', 'name', 'create_time', 'data'], 'required'],
            [['client_id', 'parent_id', 'order', 'create_time', 'status'], 'integer'],
            [['data'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacMenu::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'description' => 'Description',
            'order' => 'Order',
            'create_time' => 'Create Time',
            'status' => 'Status',
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(RbacMenu::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRbacMenus()
    {
        return $this->hasMany(RbacMenu::className(), ['parent_id' => 'id']);
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
    public function getRbacMenuPermissions()
    {
        return $this->hasMany(RbacMenuPermission::className(), ['menu_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(RbacPermission::className(), ['id' => 'permission_id'])->viaTable('{{%rbac_menu_permission}}', ['menu_id' => 'id']);
    }
}
