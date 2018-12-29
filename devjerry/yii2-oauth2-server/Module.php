<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use Yii;
use yii\base\InvalidConfigException;
use devzyj\oauth2\server\AuthorizationServer;
use devzyj\oauth2\server\ResourceServer;
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

/**
 * OAuth2 Server Module.
 * 
 * ```php
 * return [
 *     'bootstrap' => ['oauth2'],
 *     'modules' => [
 *         'oauth2' => [
 *             'class' => 'devjerry\yii2\oauth2\server\Module',
 *             'userRepositoryClass' => 'app\models\UserRepository',
 *         ],
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
     * @var string|array|callable 授权服务器。
     * @see Yii::createObject()
     */
    public $authorizationServerClass = AuthorizationServer::class;

    /**
     * @var string|array|callable 验证服务器。
     * @see Yii::createObject()
     */
    public $resourceServerClass = ResourceServer::class;
    
    /**
     * @var array 授权类型。
     */
    public $authorizeTypeClasses = [
        'code' => CodeAuthorize::class,
        'implicit' => ImplicitAuthorize::class,
    ];
    
    /**
     * @var array 权限授予类型。
     */
    public $grantTypeClasses = [
        'authorizationCode' => AuthorizationCodeGrant::class,
        'clientCredentials' => ClientCredentialsGrant::class,
        'password' => PasswordGrant::class,
        'refreshToken' => RefreshTokenGrant::class,
    ];

    /**
     * @var string|array|callable 服务器请求。
     * @see Yii::createObject()
     */
    public $serverRequestClass = ServerRequest::class;
    
    /**
     * @var string|array|callable 访问令牌存储库。
     * @see Yii::createObject()
     */
    public $accessTokenRepositoryClass = AccessTokenRepository::class;

    /**
     * @var string|array|callable 授权码存储库。
     * @see Yii::createObject()
     */
    public $authorizationCodeRepositoryClass = AuthorizationCodeRepository::class;

    /**
     * @var string|array|callable 客户端存储库。
     * @see Yii::createObject()
     */
    public $clientRepositoryClass = ClientRepository::class;

    /**
     * @var string|array|callable 更新令牌存储库。
     * @see Yii::createObject()
     */
    public $refreshTokenRepositoryClass = RefreshTokenRepository::class;

    /**
     * @var string|array|callable 权限存储库。
     * @see Yii::createObject()
     */
    public $scopeRepositoryClass = ScopeRepository::class;

    /**
     * @var string|array|callable 用户存储库。
     * @see Yii::createObject()
     */
    public $userRepositoryClass;
    
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
     * @var string 授权用户的应用组件ID。如果没有设置，则使用 `Yii::$app->getUser()`。
     */
    public $user;
    
    public $loginView;
    public $authorizeView;
    
    public $loginUrl;
    public $authorizeUrl;

    /**
     * @var callable 在验证访问令牌时，根据访问令牌实例，构造返回结果。
     * 方法应该返回一个包函访问令牌内容的数组。
     * 
     * ```php
     * function (AccessTokenEntityInterface $accessToken) {
     *     return [
     *         'access_token_id' => $accessToken->getIdentifier(),
     *         'client_id' => $accessToken->getClientIdentifier(),
     *         'user_id' => $accessToken->getUserIdentifier(),
     *         'scopes' => $accessToken->getScopeIdentifiers(),
     *     ];
     * }
     * ```
     * 
     * @see ResourceController::validateAccessTokenResult()
     */
    public $validateAccessTokenResult;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if ($this->userRepositoryClass === null) {
            throw new InvalidConfigException('The `userRepositoryClass` property must be set.');
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
}