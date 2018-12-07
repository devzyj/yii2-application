<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server;

use Yii;
use yii\base\InvalidConfigException;

/**
 * OAuth2 Server Module.
 * 
 * ```php
 * return [
 *     'bootstrap' => ['oauth2'],
 *     'modules' => [
 *         'oauth2' => ['class' => 'common\oauth2\server\Module'],
 *     ],
 * ]
 * ```
 * 
 * @property CryptKey $tokenPrivateKey 生成访问令牌的私钥
 * @property CryptKey $tokenPublicKey 验证访问令牌的公钥
 * @property CryptKey $encryptionKey 加密密钥
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    /**
     * @var string 生成访问令牌的私钥路径。
     */
    public $tokenPrivateKeyPath;
    
    /**
     * @var string 生成访问令牌的私钥密码。
     */
    public $tokenPrivateKeyPassphrase;
    
    /**
     * @var string 验证访问令牌的公钥路径。
     */
    public $tokenPublicKeyPath;

    /**
     * @var string 生成和验证访问令牌的密钥。优先级低于 [[$tokenPrivateKeyPath]] 和 [[$tokenPublicKeyPath]]。
     */
    public $tokenSecretKey;

    /**
     * @var string 加密密钥的文件路径。
     * 
     * 运行如下脚本：
     * ```
     * $ composer require defuse/php-encryption
     * $ vendor/bin/generate-defuse-key
     * ```
     * 将输出保存到文件中，并且设置参数为文件路径。
     */
    public $encryptionKeyPath;

    /**
     * @var string 加密密码。优先级低于 [[$encryptionKeyPath]]。
     */
    public $encryptionPassword;
    
    /**
     * @var array
     */
    public $entityClassMap = [
        'AccessTokenEntity' => 'common\oauth2\server\entities\AccessTokenEntity',
        'AuthCodeEntity' => 'common\oauth2\server\entities\AuthCodeEntity',
        'ClientEntity' => 'common\oauth2\server\entities\ClientEntity',
        'RefreshTokenEntity' => 'common\oauth2\server\entities\RefreshTokenEntity',
        'ScopeEntity' => 'common\oauth2\server\entities\ScopeEntity',
        'UserEntity' => 'common\oauth2\server\entities\UserEntity',
    ];
    
    /**
     * @var array
     */
    public $repositoryClassMap = [
        'AccessTokenRepository' => 'common\oauth2\server\repositories\AccessTokenRepository',
        'AuthCodeRepository' => 'common\oauth2\server\repositories\AuthCodeRepository',
        'ClientRepository' => 'common\oauth2\server\repositories\ClientRepository',
        'RefreshTokenRepository' => 'common\oauth2\server\repositories\RefreshTokenRepository',
        'ScopeRepository' => 'common\oauth2\server\repositories\ScopeRepository',
        'UserRepository' => 'common\oauth2\server\repositories\UserRepository',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if ($this->tokenSecretKey === null && ($this->tokenPrivateKeyPath === null || $this->tokenPublicKeyPath === null)) {
            throw new InvalidConfigException('The "tokenPrivateKeyPath" and "tokenPublicKeyPath", or "tokenSecretKey" property must be set.');
        } elseif ($this->encryptionKeyPath === null || $this->encryptionPassword) {
            throw new InvalidConfigException('The "encryptionKeyPath" or "encryptionPassword" property must be set.');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                "<module:({$this->uniqueId})>/authorize" => "<module>/authorize/index",
                "<module:({$this->uniqueId})>/token" => "<module>/token/index",
                "<module:({$this->uniqueId})>/resource" => "<module>/resource/index",
            ], false);
        }
        
        // set definitions
        /*foreach ($this->entityClassMap as $class => $definition) {
            $class = __NAMESPACE__ . '\\entities\\' . $class;
            Yii::$container->set($class, $definition);
        }

        foreach ($this->repositoryClassMap as $class => $definition) {
            $class = __NAMESPACE__ . '\\repositories\\' . $class;
            Yii::$container->set($class, $definition);
        }*/
    }
    
    /**
     * 获取生成访问令牌时的私钥。
     * 
     * @return CryptKey
     */
    public function getTokenPrivateKey()
    {
        if ($this->tokenPrivateKeyPath !== null) {
            return Yii::createObject(CryptKey::className(), [
                CryptKey::KEY_TYPE_PRIVATE,
                $this->tokenPrivateKeyPath,
                $this->tokenPrivateKeyPassphrase,
            ]);
        } elseif ($this->tokenSecretKey !== null) {
            return Yii::createObject(CryptKey::className(), [
                CryptKey::KEY_TYPE_SECRET,
                $this->tokenSecretKey,
            ]);
        }
    }
    
    /**
     * 获取验证访问令牌时的公钥。
     * 
     * @return CryptKey
     */
    public function getTokenPublicKey()
    {
        if ($this->tokenPublicKeyPath !== null) {
            return Yii::createObject(CryptKey::className(), [
                CryptKey::KEY_TYPE_PRIVATE,
                $this->tokenPublicKeyPath,
            ]);
        } elseif ($this->tokenSecretKey !== null) {
            return Yii::createObject(CryptKey::className(), [
                CryptKey::KEY_TYPE_SECRET,
                $this->tokenSecretKey,
            ]);
        }
    }
    
    /**
     * 获取加密密钥。
     * 
     * @return CryptKey
     */
    public function getEncryptionKey()
    {
        if ($this->encryptionKeyPath !== null) {
            return Yii::createObject(CryptKey::className(), [
                CryptKey::KEY_TYPE_PRIVATE,
                $this->encryptionKeyPath,
            ]);
        } elseif ($this->encryptionPassword !== null) {
            return Yii::createObject(CryptKey::className(), [
                CryptKey::KEY_TYPE_SECRET,
                $this->encryptionPassword,
            ]);
        }
    }
}