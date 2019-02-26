<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\rbac;

use Yii;

/**
 * This is the model class for table "{{%rbac_client}}".
 *
 * @property int $id 客户端ID
 * @property string $name 客户端名称
 * @property string $identifier API客户端标识
 * @property string $description 客户端描述
 * @property string $type 客户端类型
 * @property int $create_time 创建时间
 * 
 * @property boolean $isNormal 是否普通客户端
 * @property boolean $isSuper 是否超级客户端
 * 
 * @property User[] $users 用户
 * @property Role[] $roles 角色
 * @property Permission[] $permissions 权限
 * @property Operation[] $operations 操作
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * @var string 普通客户端。
     */
    const TYPE_NORMAL = 'NORMAL';
    
    /**
     * @var string 超级客户端。
     */
    const TYPE_SUPER = 'SUPER';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%rbac_client}}';
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
            // 设置默认值。
            [['type'], 'default', 'value' => self::TYPE_NORMAL],
            // 验证规则。
            [['name', 'identifier', 'type'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['identifier', 'type'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['identifier'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'identifier' => 'Identifier',
            'description' => 'Description',
            'type' => 'Type',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operation::class, ['client_id' => 'id']);
    }
    
    /**
     * 获取是否普通客户端。
     * 
     * @return boolean
     */
    public function getIsNormal()
    {
        return empty($this->type) || $this->type === self::TYPE_NORMAL;
    }
    
    /**
     * 获取是否超级客户端。
     * 
     * @return boolean
     */
    public function getIsSuper()
    {
        return $this->type === self::TYPE_SUPER;
    }
}
