<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components;

/**
 * JwtSignKey class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class JwtSignKey extends \yii\base\BaseObject
{
    /**
     * @var integer 密钥签名。
     */
    const KEY_TYPE_SECRET = 0;
    
    /**
     * @var integer 私钥签名。
     */
    const KEY_TYPE_PRIVATE = 1;
    
    /**
     * @var integer 签名类型。
     */
    protected $type;
    
    /**
     * @var string 密钥或者私钥、公钥的文件路径。
     */
    protected $key;

    /**
     * @var string|null 私钥的密码。
     */
    protected $passphrase;
    
    /**
     * @param integer $type 签名类型。
     * @param string $key 密钥或者私钥、公钥的文件路径。
     * @param string|null $passphrase 私钥的密码。
     */
    public function __construct($type, $key, $passphrase = null)
    {
        $this->type = $type;
        $this->key = $key;
        $this->passphrase = $passphrase;
    }

    /**
     * 是否密钥签名。
     * 
     * @return boolean
     */
    public function isSecretKey()
    {
        return $this->type === self::KEY_TYPE_SECRET;
    }
    
    /**
     * 是否私钥签名。
     * 
     * @return boolean
     */
    public function isPrivateKey()
    {
        return $this->type === self::KEY_TYPE_PRIVATE;
    }
    
    /**
     * 获取密钥或者私钥、公钥的文件路径。
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * 获取私钥的密码。
     * 
     * @return string|null
     */
    public function getPassphrase()
    {
        return $this->passphrase;
    }
}
