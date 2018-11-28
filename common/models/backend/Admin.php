<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\backend;

use Yii;
use common\validators\backend\UsernameValidator;
use common\validators\backend\PasswordValidator;
use common\validators\backend\NicknameValidator;

/**
 * This is the model class for table "{{%backend_admin}}".
 *
 * @property int $id 管理员ID
 * @property string $username 用户名
 * @property string $password_hash 密码 HASH 值
 * @property string $hash_code HASH 附加值
 * @property string $nickname 昵称
 * @property int $create_time 创建时间
 * @property int $status 状态（0=禁用；1=可用）
 * @property string $avatar 头像信息
 * @property string $allowed_ips 允许登录的 IPs
 * 
 * @property AdminEmail[] $emails 管理员邮箱
 * @property AdminMobile[] $mobiles 管理员手机号码
 * @property AdminLoginLog[] $loginLogs 管理员登录日志
 *
 * @property string $password 设置的密码
 * @property boolean $isValid 管理员是否有效
 * @property array $allowedIPs 允许登录的IPs
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
     * @var string // 设置的密码，为空则保持不变。
     */
    private $_password;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_admin}}';
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
            // 过滤和处理数据。
            [['username'], 'filter', 'filter' => 'strtolower'],
            [['avatar', 'allowed_ips'], 'default', 'value' => ''],
            // 验证规则。
            [['username', 'nickname'], 'required'],
            [['username'], UsernameValidator::class],
            [['password'], PasswordValidator::class],
            [['nickname'], NicknameValidator::class],
            [['status'], 'boolean'],
            [['avatar', 'allowed_ips'], 'string', 'max' => 5000],
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
            'avatar' => 'Avatar',
            'allowed_ips' => 'Allowed Ips',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(AdminEmail::class, ['admin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoginLogs()
    {
        return $this->hasMany(AdminLoginLog::class, ['admin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMobiles()
    {
        return $this->hasMany(AdminMobile::class, ['admin_id' => 'id']);
    }
    
    /**
     * 生成 HASH 附加值。
     * 
     * @return string 生成的 HASH 附加值。
     */
    public static function generateHashCode()
    {
        return (string) rand(100000, 999999);
    }
    
    /**
     * HASH 密码。
     * 
     * @param string $password 需要 HASH 的密码。
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
     * 获取管理员是否有效。
     * 
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->status === self::STATUS_ENABLED;
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
     * @param string $ip 需要检查的IP地址。
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
