<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
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
 * @property boolean $isNormal 是否为普通类型的客户端
 * @property boolean $isManager 是否为管理类型的客户端
 * 
 * @property RbacOperation[] $rbacOperations 操作
 * @property RbacPermission[] $rbacPermissions 权限
 * @property RbacRole[] $rbacRoles 角色
 * @property RbacUser[] $rbacUsers 用户
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacClient extends \yii\db\ActiveRecord
{
    /**
     * @var string 普通类型的客户端。
     */
    const TYPE_NORMAL = 'NORMAL';
    
    /**
     * @var string 管理类型的客户端。
     */
    const TYPE_MANAGER = 'MANAGER';
    
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
            // 默认值。
            [['type'], 'default', 'value' => self::TYPE_NORMAL],
            // 验证规则。
            [['identifier', 'name', 'type'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['identifier', 'type'], 'string', 'max' => 20],
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
     * 获取操作查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacOperations()
    {
        return $this->hasMany(RbacOperation::class, ['client_id' => 'id']);
    }

    /**
     * 获取权限查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacPermissions()
    {
        return $this->hasMany(RbacPermission::class, ['client_id' => 'id']);
    }

    /**
     * 获取角色查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacRoles()
    {
        return $this->hasMany(RbacRole::class, ['client_id' => 'id']);
    }

    /**
     * 获取用户查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRbacUsers()
    {
        return $this->hasMany(RbacUser::class, ['client_id' => 'id']);
    }
    
    /**
     * 获取是否为普通类型的客户端。
     * 
     * @return boolean
     */
    public function getIsNormal()
    {
        return empty($this->type) || $this->type === self::TYPE_NORMAL;
    }
    
    /**
     * 获取是否为管理类型的客户端。
     * 
     * @return boolean
     */
    public function getIsManager()
    {
        return $this->type === self::TYPE_MANAGER;
    }
}
