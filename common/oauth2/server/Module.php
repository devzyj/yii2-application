<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server;

use Yii;
use yii\base\InvalidConfigException;
use common\oauth2\server\components\JwtSignKey;

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
 * @property JwtSignKey $tokenPrivateKey 生成令牌的私钥
 * @property JwtSignKey $tokenPublicKey 验证令牌的公钥
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    /**
     * @var array
     */
    public $entityClassMap = [
        'AccessTokenEntity' => 'common\oauth2\server\components\entities\AccessTokenEntity',
        'AuthCodeEntity' => 'common\oauth2\server\components\entities\AuthCodeEntity',
        'ClientEntity' => 'common\oauth2\server\components\entities\ClientEntity',
        'RefreshTokenEntity' => 'common\oauth2\server\components\entities\RefreshTokenEntity',
        'ScopeEntity' => 'common\oauth2\server\components\entities\ScopeEntity',
        'UserEntity' => 'common\oauth2\server\components\entities\UserEntity',
    ];

    /**
     * @var array
     */
    public $repositoryClassMap = [
        'AccessTokenRepository' => 'common\oauth2\server\components\repositories\AccessTokenRepository',
        'AuthCodeRepository' => 'common\oauth2\server\components\repositories\AuthCodeRepository',
        'ClientRepository' => 'common\oauth2\server\components\repositories\ClientRepository',
        'RefreshTokenRepository' => 'common\oauth2\server\components\repositories\RefreshTokenRepository',
        'ScopeRepository' => 'common\oauth2\server\components\repositories\ScopeRepository',
        'UserRepository' => 'common\oauth2\server\components\repositories\UserRepository',
    ];
    
    /**
     * @var string 生成令牌的私钥路径。
     */
    public $tokenPrivateKeyPath;
    
    /**
     * @var string 生成令牌的私钥密码。
     */
    public $tokenPrivateKeyPassphrase;
    
    /**
     * @var string 验证令牌的公钥路径。
     */
    public $tokenPublicKeyPath;

    /**
     * @var string 生成和验证令牌的签名密钥。优先级低于私钥和公钥。
     */
    public $tokenSignKey;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if ($this->tokenSignKey === null && ($this->tokenPrivateKeyPath === null || $this->tokenPublicKeyPath === null)) {
            throw new InvalidConfigException('The "tokenPrivateKeyPath" and "tokenPublicKeyPath", or "tokenSignKey" property must be set.');
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
     * 获取生成令牌时的私钥。
     * 
     * @return JwtSignKey
     */
    public function getTokenPrivateKey()
    {
        if ($this->tokenPrivateKeyPath !== null) {
            return Yii::createObject(JwtSignKey::className(), [
                JwtSignKey::KEY_TYPE_PRIVATE,
                Yii::getAlias($this->tokenPrivateKeyPath),
                $this->tokenPrivateKeyPassphrase,
            ]);
        } elseif ($this->tokenSignKey !== null) {
            return Yii::createObject(JwtSignKey::className(), [
                JwtSignKey::KEY_TYPE_SECRET,
                $this->tokenSignKey,
            ]);
        }
    }
    
    /**
     * 获取验证令牌时的公钥。
     * 
     * @return JwtSignKey
     */
    public function getTokenPublicKey()
    {
        if ($this->tokenPublicKeyPath !== null) {
            return Yii::createObject(JwtSignKey::className(), [
                JwtSignKey::KEY_TYPE_PRIVATE,
                Yii::getAlias($this->tokenPublicKeyPath),
            ]);
        } elseif ($this->tokenSignKey !== null) {
            return Yii::createObject(JwtSignKey::className(), [
                JwtSignKey::KEY_TYPE_SECRET,
                $this->tokenSignKey,
            ]);
        }
    }
}