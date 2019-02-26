<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
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
 * @property RbacRoleUser[] $rbacRoleUsers 角色与用户关联数据
 * @property RbacRole[] $rbacRoles 角色
 * @property RbacClient $rbacClient 客户端
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
            [['client_id', 'identifier'], 'required'],
            [['client_id'], 'integer'],
            [['identifier', 'description'], 'string', 'max' => 255],
            [['identifier'], 'unique', 'targetAttribute' => ['client_id', 'identifier']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => RbacClient::class, 'targetAttribute' => ['client_id' => 'id']],
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
     * 获取角色与用户关联查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoleUsers()
    {
        return $this->hasMany(RbacRoleUser::class, ['user_id' => 'id']);
    }

    /**
     * 获取角色查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoles()
    {
        return $this->hasMany(RbacRole::class, ['id' => 'role_id'])->viaTable(RbacRoleUser::tableName(), ['user_id' => 'id']);
    }

    /**
     * 获取客户端查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacClient()
    {
        return $this->hasOne(RbacClient::class, ['id' => 'client_id']);
    }
}
