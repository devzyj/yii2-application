<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\backend;

use Yii;

/**
 * This is the model class for table "{{%backend_client}}".
 *
 * @property string $id ID
 * @property string $name 名称
 * @property string $secret 密钥
 * @property string $description 描述
 * @property int $create_time 创建时间
 * @property int $status 状态（0=禁用；1=可用）
 * @property string $allowed_ips 允许访问的 IPs
 * @property string $allowed_apis 允许访问的 APIs
 * @property int $access_token_duration 访问令牌的持续时间
 * @property int $refresh_token_duration 刷新令牌的持续时间
 *
 * @property AdminLoginLog[] $adminLoginLogs 管理员登录日志
 * 
 * @property boolean $isValid 客户端是否有效
 * @property array $allowedIPs 允许访问的 IPs
 * @property array $allowedAPIs 允许访问的 APIs
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Client extends \yii\db\ActiveRecord
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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_client}}';
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
            'attributesBehavior' => [
                'class' => 'yii\behaviors\AttributesBehavior',
                'preserveNonEmptyValues' => true,
                'attributes' => [
                    'id' => [
                        self::EVENT_BEFORE_INSERT => $fn = [static::class, 'generateId'],
                        self::EVENT_BEFORE_UPDATE => $fn,
                    ],
                    'secret' => [
                        self::EVENT_BEFORE_INSERT => $fn = [static::class, 'generateSecret'],
                        self::EVENT_BEFORE_UPDATE => $fn,
                    ],
                ]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'boolean'],
            [['name'], 'string', 'max' => 50],
            [['description', 'allowed_ips', 'allowed_apis'], 'string', 'max' => 255],
            [['access_token_duration', 'refresh_token_duration'], 'integer'],
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
            'name' => 'Name',
            'secret' => 'Secret',
            'description' => 'Description',
            'create_time' => 'Create Time',
            'status' => 'Status',
            'allowed_ips' => 'Allowed Ips',
            'allowed_apis' => 'Allowed Apis',
            'access_token_duration' => 'Access Token Duration',
            'refresh_token_duration' => 'Refresh Token Duration',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminLoginLogs()
    {
        return $this->hasMany(AdminLoginLog::class, ['client_id' => 'id']);
    }
    
    /**
     * 生成客户端 ID。
     * 
     * @return string
     */
    public static function generateId()
    {
        return substr(md5(microtime().rand(1000, 9999)), 8, 16);
    }

    /**
     * 生成客户端密钥。
     * 
     * @return string
     */
    public static function generateSecret()
    {
        return md5(microtime().rand(1000, 9999));
    }
    
    /**
     * 获取客户端是否有效。
     * 
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->status === self::STATUS_ENABLED;
    }
    
    /**
     * 获取客户端允许访问的 IPs。
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
     * 检查 IP 是否被允许。
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
    
    /**
     * 获取客户端允许访问的 APIs。
     * 
     * @return array
     */
    public function getAllowedAPIs()
    {
        $allowedAPIs = trim($this->allowed_apis, ',');
        if ($allowedAPIs) {
            return explode(',', $allowedAPIs);
        }
        
        return [];
    }
    
    /**
     * 确保 APIs 格式正确。
     * 
     * 1. 不处理单个字符 `*`;
     * 2. 字符串中如果有字符 `*`，则去除未尾的 `/`，并且在开头添加 '/'。
     * 3. 字符串中如果没有字符 `*`，则在开头和未尾都添加 `/`。
     * 
     * 返回值的格式可能包括以下几种：
     * [
     *     '*',
     *     '/api/user-*',
     *     '/api/users/*',
     *     '/api/users/create/'
     * ]
     * 
     * @param array $list API 列表。
     * @return array 
     */
    public function ensureAllowedAPIs($list)
    {
        foreach ($list as $key => $value) {
            if ($value === '*') {
                $list[$key] = $value;
                continue;
            }
            
            $value = '/' . trim($value, '/');
            if (strpos($value, '*') === false) {
                $value .= '/';
            }
            
            $list[$key] = $value;
        }
        
        return $list;
    }
    
    /**
     * 检查 API 是否被允许。
     * 
     * @param string $api 需要检查的 API。
     * @return boolean
     */
    public function checkAllowedApi($api)
    {
        $api = '/' . trim($api, '/') . '/';
        $list = $this->ensureAllowedAPIs($this->getAllowedAPIs());
        foreach ($list as $allowed) {
            if ($allowed === '*' || $allowed === $api || (($pos = strpos($allowed, '*')) !== false && !strncmp($api, $allowed, $pos))) {
                return true;
            }
        }
        
        return false;
    }
}
