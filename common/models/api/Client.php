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
 * This is the model class for table "{{%client}}".
 *
 * @property int $id 客户端ID
 * @property string $name 客户端名称
 * @property string $description 客户端描述
 * @property string $identifier 客户端标识
 * @property string $secret 客户端密钥
 * @property int $create_time 创建时间
 * @property int $status 客户端状态（0=禁用；1=可用）
 * @property int $token_expires_in 令牌过期时间（秒）
 * @property int $rate_limit_count 速率限制次数
 * @property int $rate_limit_seconds 速率限制秒数
 * @property string $allowed_ips 允许访问的IPs
 * @property string $allowed_apis 允许访问的APIs
 *
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
        return '{{%client}}';
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
                    'identifier' => [
                        self::EVENT_BEFORE_INSERT => [$this, 'generateIdentifier'],
                        self::EVENT_BEFORE_UPDATE => [$this, 'generateIdentifier'],
                    ],
                    'secret' => [
                        self::EVENT_BEFORE_INSERT => [$this, 'generateSecret'],
                        self::EVENT_BEFORE_UPDATE => [$this, 'generateSecret'],
                    ],
                ]
            ],
            'identifierCacheBehavior' => [
                'class' => 'devzyj\behaviors\ActiveCacheBehavior',
                'baseModelCacheKey' => ['Api', 'Client', 'Identifier'],
                'keyAttributes' => ['identifier'],
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
            'description' => Yii::t('app', 'Description'),
            'identifier' => Yii::t('app', 'Identifier'),
            'secret' => Yii::t('app', 'Secret'),
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
     * 通过客户端标识，从缓存或者数据库中查找并返回一个客户端对像。
     *
     * @param string $identifier 客户端标识。
     * @param integer $duration 设置缓存的持续时间（秒）。如果为 `null`，则使用默认值。
     * @param \yii\caching\Dependency $dependency 设置缓存的依赖项。如果依赖项发生了变化，那么再次获取数据时，缓存将失效。
     * @return static|null 匹配条件的 ActiveRecord 实例，如果没有匹配，则为 `null`。
     */
    public static function findOrSetOneByIdentifier($identifier, $duration = null, $dependency = null)
    {
        return static::findOrSetOneByAttribute($identifier, $duration, $dependency);
    }
    
    /**
     * 生成客户端标识。
     * 
     * @return string
     */
    public function generateIdentifier()
    {
        return substr(md5(microtime().rand(1000, 9999)), 8, 16);
    }

    /**
     * 生成客户端密钥。
     * 
     * @return string
     */
    public function generateSecret()
    {
        return md5(microtime().rand(1000, 9999));
    }
    
    /**
     * 获取允许访问的 IPs。
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
     * 获取允许访问的 APIs。
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
     * 检查允许访问的 IPs。
     * 
     * @param string $ip 需要检查的IP地址。
     * @return boolean
     */
    public function checkAllowedIPs($ip)
    {
        $allowedIPs = $this->getAllowedIPs();
        if ($allowedIPs) {
            foreach ($allowedIPs as $allowed) {
                if ($allowed === '*' || $allowed === $ip || (($pos = strpos($allowed, '*')) !== false && !strncmp($ip, $allowed, $pos))) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 检查允许访问的 APIs。
     * 
     * @param string $api 需要检查的api接口。（以正斜杠 `/` 开始和结束的字符串）
     * @return boolean
     */
    public function checkAllowedAPIs($api)
    {
        $allowedAPIs = $this->getAllowedAPIs();
        if ($allowedAPIs) {
            $api = '/' . trim($api, '/') . '/';
            foreach ($allowedAPIs as $allowed) {
                if ($allowed === '*' || $allowed === $api || (($pos = strpos($allowed, '*')) !== false && !strncmp($api, $allowed, $pos))) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 删除缓存。
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