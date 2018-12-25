<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use Yii;
use yii\web\Request;
use yii\web\User;
use devzyj\oauth2\server\authorizes\CodeAuthorize;
use devzyj\oauth2\server\authorizes\ImplicitAuthorize;
use devzyj\oauth2\server\grants\AuthorizationCodeGrant;
use devzyj\oauth2\server\grants\ClientCredentialsGrant;
use devzyj\oauth2\server\grants\PasswordGrant;
use devzyj\oauth2\server\grants\RefreshTokenGrant;
use devjerry\yii2\oauth2\server\ServerRequest;
use devjerry\yii2\oauth2\server\repositories\AccessTokenRepository;
use devjerry\yii2\oauth2\server\repositories\AuthorizationCodeRepository;
use devjerry\yii2\oauth2\server\repositories\ClientRepository;
use devjerry\yii2\oauth2\server\repositories\RefreshTokenRepository;
use devjerry\yii2\oauth2\server\repositories\ScopeRepository;
use devjerry\yii2\oauth2\server\repositories\UserRepository;
use devjerry\yii2\oauth2\server\behaviors\ServerRequestBehavior;

/**
 * OAuth2 Server Module.
 * 
 * ```php
 * return [
 *     'bootstrap' => ['oauth2'],
 *     'modules' => [
 *         'oauth2' => ['class' => 'devjerry\yii2\oauth2\server\Module'],
 *     ],
 * ]
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    /**
     * @var array 授权类型。
     */
    public $authorizeTypes = [
        'code' => CodeAuthorize::class,
        'implicit' => ImplicitAuthorize::class,
    ];
    
    /**
     * @var array 权限授予类型。
     */
    public $grantTypes = [
        'authorizationCode' => AuthorizationCodeGrant::class,
        'clientCredentials' => ClientCredentialsGrant::class,
        'password' => PasswordGrant::class,
        'refreshToken' => RefreshTokenGrant::class,
    ];

    /**
     * @var string|array|callable 服务器请求。
     * @see Yii::createObject()
     */
    public $serverRequest = ServerRequest::class;
    
    /**
     * @var string|array|callable 访问令牌存储库。
     * @see Yii::createObject()
     */
    public $accessTokenRepository = AccessTokenRepository::class;

    /**
     * @var string|array|callable 授权码存储库。
     * @see Yii::createObject()
     */
    public $authorizationCodeRepository = AuthorizationCodeRepository::class;

    /**
     * @var string|array|callable 客户端存储库。
     * @see Yii::createObject()
     */
    public $clientRepository = ClientRepository::class;

    /**
     * @var string|array|callable 更新令牌存储库。
     * @see Yii::createObject()
     */
    public $refreshTokenRepository = RefreshTokenRepository::class;

    /**
     * @var string|array|callable 权限存储库。
     * @see Yii::createObject()
     */
    public $scopeRepository = ScopeRepository::class;

    /**
     * @var string|array|callable 用户存储库。
     * @see Yii::createObject()
     */
    public $userRepository = UserRepository::class;
    
    /**
     * @var array 默认权限。
     */
    public $defaultScopes = [];
    
    /**
     * @var integer 访问令牌的持续时间，默认一小时。
     */
    public $accessTokenDuration = 3600;
    
    /**
     * @var string|array 访问令牌密钥。
     */
    public $accessTokenCryptKey;

    /**
     * @var string 访问令牌在查询参数中的名称。
     */
    public $accessTokenQueryParam = 'access-token';
    
    /**
     * @var integer 授权码的持续时间，默认十分钟。
     */
    public $authorizationCodeDuration = 600;
    
    /**
     * @var array 授权码密钥。
     */
    public $authorizationCodeCryptKey;
    
    /**
     * @var integer 更新令牌的持续时间，默认三十天。
     */
    public $refreshTokenDuration = 2592000;
    
    /**
     * @var array 更新令牌密钥。
     */
    public $refreshTokenCryptKey;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // 服务器请求实例。
        /*if ($this->serverRequest === null) {
            $this->serverRequest = Yii::$app->getRequest();
        } elseif (is_string($this->serverRequest)) {
            $this->serverRequest = Yii::$app->get($this->serverRequest);
        } elseif (is_array($this->serverRequest)) {
            $this->serverRequest = Yii::createObject($this->serverRequest);
        }
        
        // 添加服务器请求行为。
        $this->serverRequest->attachBehavior('OAuthServerRequestBehavior', ServerRequestBehavior::class);*/
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
}