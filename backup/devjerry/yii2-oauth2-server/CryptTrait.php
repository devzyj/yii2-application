<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use devjerry\yii2\oauth2\server\CryptKey;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

/**
 * CryptTrait 提供了加密、解密数据的方法。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait CryptTrait
{
    /**
     * 加密数据。
     * 
     * @param string $plaintext 明文。
     * @param CryptKey|Key|string $key 密钥。
     * @return string 密文。
     */
    protected function encrypt($plaintext, $key)
    {
        if ($key instanceof CryptKey) {
            $key = $this->processEncryptionKey($key);
        }
        
        if ($key instanceof Key) {
            return Crypto::encrypt($plaintext, $key);
        }
        
        return Crypto::encryptWithPassword($plaintext, $key);
    }

    /**
     * 解密数据。
     * 
     * @param string $ciphertext 密文。
     * @param CryptKey|Key|string $key 密钥。
     * @return string 明文。
     */
    protected function decrypt($ciphertext, $key)
    {
        if ($key instanceof CryptKey) {
            $key = $this->processEncryptionKey($key);
        }
        
        if ($key instanceof Key) {
            return Crypto::decrypt($ciphertext, $key);
        }
        
        return Crypto::decryptWithPassword($ciphertext, $key);
    }
    
    /**
     * @param CryptKey $key
     * @return Key|string
     */
    private function processEncryptionKey($key)
    {
        if ($key->isPrivateKey()) {
            $keyAscii = file_get_contents($key->getKey());
            return Key::loadFromAsciiSafeString($keyAscii);
        }
        
        return $key->getKey();
    }
}
