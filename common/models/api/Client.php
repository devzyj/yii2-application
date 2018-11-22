<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\models\api;

use Yii;
use devzyj\behaviors\ActiveCacheBehaviorTrait;

/**
 * This is the model class for table "{{%api_client}}".
 *
 * @property string $id 客户端ID
 * @property string $name 客户端名称
 * @property string $secret 客户端密钥
 * @property string $description 客户端描述
 * @property int $create_time 创建时间
 * @property int $status 客户端状态（0=禁用；1=可用）
 * @property int $token_expires_in 令牌过期时间（秒）
 * @property int $rate_limit_count 速率限制次数
 * @property int $rate_limit_seconds 速率限制秒数
 * @property string $allowed_ips 允许访问的IPs
 * @property string $allowed_apis 允许访问的APIs
 *
 * @property boolean $isValid 客户端是否有效。
 * @property array $rateLimitContents 客户端的速率限制
 * @property array $allowedIPs 允许访问的IPs
 * @property array $allowedAPIs 允许访问的APIs
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Client extends \yii\db\ActiveRecord
{
    use ActiveCacheBehaviorTrait;
    
    /**
     * @var integer 状态 - 禁用的。
     */
    const STATUS_DISABLED = 0;

    /**
     * @var integer 状态 - 可用的。
     */
    const STATUS_ENABLED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%api_client}}';
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
                        self::EVENT_BEFORE_INSERT => [static::class, 'generateId'],
                        self::EVENT_BEFORE_UPDATE => [static::class, 'generateId'],
                    ],
                    'secret' => [
                        self::EVENT_BEFORE_INSERT => [static::class, 'generateSecret'],
                        self::EVENT_BEFORE_UPDATE => [static::class, 'generateSecret'],
                    ],
                ]
            ],
            'primaryKeyCacheBehavior' => [
                'class' => '\devzyj\behaviors\ActiveCacheBehavior',
                //'cache' => Yii::createObject('\yii\caching\DummyCache'), // configure no cache
                'baseModelCacheKey' => ['Api', 'Client', 'PrimaryKey'],
                'defaultDuration' => 86400, // 24 hours
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
            [['name'], 'trim'],
            [['description', 'allowed_ips', 'allowed_apis'], 'default', 'value' => ''],
            [['status'], 'default', 'value' => self::STATUS_DISABLED],
            [['token_expires_in', 'rate_limit_count', 'rate_limit_seconds'], 'default', 'value' => 0],
            // 验证规则。
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['status'], 'boolean'],
            [['token_expires_in', 'rate_limit_count', 'rate_limit_seconds'], 'integer', 'integerOnly' => true, 'min' => 0],
            [['allowed_ips', 'allowed_apis'], 'string'],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'secret' => Yii::t('app', 'Secret'),
            'description' => Yii::t('app', 'Description'),
            'create_time' => Yii::t('app', 'Create Time'),
            'status' => Yii::t('app', 'Status'),
            'token_expires_in' => Yii::t('app', 'Token Expires In'),
            'rate_limit_count' => Yii::t('app', 'Rate Limit Count'),
            'rate_limit_seconds' => Yii::t('app', 'Rate Limit Seconds'),
            'allowed_ips' => Yii::t('app', 'Allowed IPs'),
            'allowed_apis' => Yii::t('app', 'Allowed APIs'),
        ];
    }
    
    /**
     * 生成客户端ID。
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
     * 获取客户端的速率限制。
     *
     * @return array
     */
    public function getRateLimitContents()
    {
        return [
            $this->rate_limit_count,
            $this->rate_limit_seconds ? $this->rate_limit_seconds : 1
        ];
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
     * @param array $list API列表。
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

    /**
     * 通过客户端ID，从缓存或者数据库中查找并返回一个客户端对像。
     *
     * @param string $id 客户端ID。
     * @param integer $duration 设置缓存的持续时间（秒）。如果为 `null`，则使用默认值。
     * @param \yii\caching\Dependency $dependency 设置缓存的依赖项。如果依赖项发生了变化，那么再次获取数据时，缓存将失效。
     * @return static|null 匹配条件的 ActiveRecord 实例，如果没有匹配，则为 `null`。
     */
    public static function findOrSetOneById($id, $duration = null, $dependency = null)
    {
        return static::findOrSetOneByAttribute($id, $duration, $dependency);
    }
    
    /**
     * 删除客户端缓存。
     * 
     * @return boolean 是否删除成功。
     */
    public function deleteCache()
    {
        if ($this->existsActiveCache()) {
            return $this->deleteActiveCache();
        }
        
        return true;
    }
}
