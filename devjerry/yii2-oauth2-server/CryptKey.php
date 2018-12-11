<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use Yii;

/**
 * CryptKey 用于判断是私钥加密，还是密钥字符串加密，并且存放密钥字符串，或者私钥、公钥的文件路径。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CryptKey extends \yii\base\BaseObject
{
    /**
     * @var integer 密钥。
     */
    const KEY_TYPE_SECRET = 0;
    
    /**
     * @var integer 私钥。
     */
    const KEY_TYPE_PRIVATE = 1;
    
    /**
     * @var integer 类型。
     */
    private $_type;
    
    /**
     * @var string 密钥字符串，或者私钥、公钥的文件路径。
     */
    private $_key;

    /**
     * @var string|null 私钥的密码。
     */
    private $_passphrase;
    
    /**
     * @param integer $type 类型。
     * @param string $key 密钥字符串，或者私钥、公钥的文件路径（路径支持别名）。
     * @param string|null $passphrase 私钥的密码。
     * @param array $config 用于初始化对象属性的配置。
     */
    public function __construct($type, $key, $passphrase = null, $config = [])
    {
        $this->_type = $type;
        
        if ($type === self::KEY_TYPE_PRIVATE && strpos('@', $key) === 0) {
            $this->_key = Yii::getAlias($key);
        } else {
            $this->_key = $key;
        }
        
        $this->_passphrase = $passphrase;

        parent::__construct($config);
    }

    /**
     * 是否密钥。
     * 
     * @return boolean
     */
    public function isSecretKey()
    {
        return $this->_type === self::KEY_TYPE_SECRET;
    }
    
    /**
     * 是否私钥。
     * 
     * @return boolean
     */
    public function isPrivateKey()
    {
        return $this->_type === self::KEY_TYPE_PRIVATE;
    }
    
    /**
     * 获取密钥字符串，或者私钥、公钥的文件路径。
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * 获取私钥的密码。
     * 
     * @return string|null
     */
    public function getPassphrase()
    {
        return $this->_passphrase;
    }
}
