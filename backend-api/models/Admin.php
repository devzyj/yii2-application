<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;
use backendApi\validators\UsernameValidator;
use backendApi\validators\PasswordValidator;
use backendApi\validators\NicknameValidator;
use backendApi\validators\MobileValidator;

/**
 * This is the model class for table "{{%admin}}".
 *
 * @property int $id ID
 * @property string $username 用户名
 * @property string $password_hash 密码 hash 值
 * @property string $hash_code 密码 hash 附加值
 * @property string $nickname 昵称
 * @property int $create_time 创建时间
 * @property int $status 状态（0=禁用；1=可用）
 * @property string $email 邮箱地址
 * @property string $mobile 手机号码
 * @property string $avatar 头像信息
 * @property string $allowed_ips 允许登录的 IPs
 *
 * @property AdminLoginLog[] $adminLoginLogs 管理员登录日志
 * 
 * @property string $password 设置的密码
 * @property boolean $isValid 管理员是否有效
 * @property array $allowedIPs 允许登录的 IPs
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * @var integer 状态 - 禁用的。
     */
    const STATUS_DISABLED = 0;

    /**
     * @var integer 状态 - 启用的。
     */
    const STATUS_ENABLED = 1;
    
    /**
     * @var string 用户密码。
     */
    private $_password;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }
    
    /**
     * {@inheritdoc}
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
            [['username', 'nickname'], 'required'],
            [['status'], 'boolean'],
            [['username', 'nickname', 'mobile'], 'string', 'max' => 20],
            [['email', 'avatar', 'allowed_ips'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['username'], UsernameValidator::class],
            [['password'], PasswordValidator::class],
            [['nickname'], NicknameValidator::class],
            [['mobile'], MobileValidator::class],
            [['username'], 'unique'],
            [['nickname'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'hash_code' => 'Hash Code',
            'nickname' => 'Nickname',
            'create_time' => 'Create Time',
            'status' => 'Status',
            'email' => 'Email',
            'mobile' => 'Mobile',
            'avatar' => 'Avatar',
            'allowed_ips' => 'Allowed Ips',
        ];
    }

    /**
     * 获取管理员登录日志。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getAdminLoginLogs()
    {
        return $this->hasMany(AdminLoginLog::class, ['admin_id' => 'id']);
    }

    /**
     * 通过用户名，查询并返回一个管理员模型。
     * 
     * @param string $username 用户名。
     * @return static|null 管理员模型实例，如果没有匹配到，则为 `null`。
     */
    public static function findOneByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }
    
    /**
     * 获取管理员是否有效。
     * 
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->status === self::STATUS_ENABLED;
    }
    
    /**
     * 验证密码是否正确。
     * 
     * @param string $password 需要验证的密码。
     * @return boolean 是否正确。
     */
    public function validatePassword($password)
    {
        $passwordHash = $this->hashPassword($password);
        return $this->password_hash === $passwordHash;
    }
    
    /**
     * hash 密码。
     * 
     * @param string $password 需要 hash 的密码。
     * @return string HASH 后的密码。
     */
    public function hashPassword($password)
    {
        return md5($password . $this->hash_code);
    }
    
    /**
     * 获取设置的密码。
     * 
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }
    
    /**
     * 设置密码。
     * 
     * @param string $value
     */
    public function setPassword($value)
    {
        $this->_password = $value;
        
        // 设置 [[$hash_code]] 和 [[$password_hash]] 的值。
        if (empty($this->_password)) {
            $this->hash_code = '';
            $this->password_hash = '';
        } else {
            $this->hash_code = static::generateHashCode();
            $this->password_hash = $this->hashPassword($this->_password);
        }
    }
    
    /**
     * 生成 hash 附加值。
     * 
     * @return string 生成的 hash 附加值。
     */
    public static function generateHashCode()
    {
        return (string) rand(100000, 999999);
    }
    
    /**
     * 获取管理员允许登录的 IPs。
     *
     * @return array
     */
    public function getAllowedIPs()
    {
        $allowedIPs = trim($this->allowed_ips, ',');
        if ($allowedIPs) {
            return explode(',', $allowedIPs);
        }
        
        return [];
    }
    
    /**
     * 检查 IP 是否被允许登录。
     * 
     * @param string $ip 需要检查的 IP 地址。
     * @return boolean
     */
    public function checkAllowedIp($ip)
    {
        foreach ($this->getAllowedIPs() as $allowed) {
            if ($allowed === '*' || $allowed === $ip || (($pos = strpos($allowed, '*')) !== false && !strncmp($ip, $allowed, $pos))) {
                return true;
            }
        }
        
        return false;
    }
}
