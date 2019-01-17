<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use Yii;
use yii\db\Connection;
use yii\web\User;
use devzyj\oauth2\server\authorizes\CodeAuthorize;
use devzyj\oauth2\server\authorizes\ImplicitAuthorize;
use devzyj\oauth2\server\authorizes\AuthorizeRequestInterface;
use devzyj\oauth2\server\grants\AuthorizationCodeGrant;
use devzyj\oauth2\server\grants\ClientCredentialsGrant;
use devzyj\oauth2\server\grants\PasswordGrant;
use devzyj\oauth2\server\grants\RefreshTokenGrant;
use devjerry\yii2\oauth2\server\interfaces\OAuthLoginFormInterface;
use devjerry\yii2\oauth2\server\interfaces\AuthorizationFormInterface;

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
     * @var array 授权类型。
     */
    public $authorizeTypeClasses = [
        CodeAuthorize::class,
        ImplicitAuthorize::class,
    ];
    
    /**
     * @var array 权限授予类型。
     */
    public $grantTypeClasses = [
        AuthorizationCodeGrant::class,
        ClientCredentialsGrant::class,
        PasswordGrant::class,
        RefreshTokenGrant::class,
    ];

    /**
     * @var string|array|callable 用户存储库。
     * @see Yii::createObject()
     */
    public $userRepositoryClass;

    /**
     * @var array 类映射。
     */
    public $classMap = [];
    
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
     * @var string 验证访问令牌时，在查询参数中的名称。
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
     * @var Connection|array|string 数据库连接对象，或数据库连接的应用程序组件ID。如果没有设置，则使用 `Yii::$app->getDb()`。
     */
    //public $db;
    
    /**
     * @var string|array 授权用户的应用组件ID或配置。如果没有设置，则使用 `Yii::$app->getUser()`。
     */
    public $user;
    
    /**
     * @var string|array 登录地址。如果没有设置，则使用 ['/MODULE_ID/login']。
     */
    public $loginUrl;

    /**
     * @var string|array 授权地址。如果没有设置，则使用 ['/MODULE_ID/authorization']。
     */
    public $authorizationUrl;

    /**
     * @var OAuthLoginFormInterface 登录页面表单模型类名。
     */
    public $loginFormClass;
    
    /**
     * @var AuthorizationFormInterface 授权页面表单模型类名。
     */
    public $authorizationFormClass;

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
        
        if ($this->loginUrl === null) {
            $this->loginUrl = ['/' . $this->uniqueId . '/login'];
        }

        if ($this->authorizationUrl === null) {
            $this->authorizationUrl = ['/' . $this->uniqueId . '/authorization'];
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
                "<module:({$this->uniqueId})>/login" => "<module>/authorize/login",
                "<module:({$this->uniqueId})>/authorization" => "<module>/authorize/authorization",
                "<module:({$this->uniqueId})>/token" => "<module>/token/index",
                "<module:({$this->uniqueId})>/resource" => "<module>/resource/index",
            ], false);
        }
        
        // set definitions
        foreach ($this->classMap as $class => $definition) {
            Yii::$container->set($class, $definition);
        }
    }
    
    /**
     * 获取授权用户。
     * 
     * @return User
     */
    public function getUser()
    {
        if ($this->user === null) {
            return Yii::$app->getUser();
        } elseif (is_string($this->user)) {
            return Yii::$app->get($this->user);
        }
        
        return Yii::createObject($this->user);
    }

    
    
    
    //const AUTHORIZE_REQUEST_KEY = 'OAUTH2_AUTHORIZE_REQUEST';
    
    /**
     * 获取保存授权请求的名称。
     
    protected function getAuthorizeRequestName()
    {
        return strtr($this->uniqueId, ['/' => '_']) . '_OAUTH2_AUTHORIZE_REQUEST';
    }*/
    
    /**
     * 获取授权请求。
     * 
     * @return AuthorizeRequestInterface
     
    public function getAuthorizeRequest()
    {
        return Yii::$app->getSession()->get($this->getAuthorizeRequestName());
    }*/
    
    /**
     * 设置授权请求。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest
     
    public function setAuthorizeRequest(AuthorizeRequestInterface $authorizeRequest)
    {
        Yii::$app->getSession()->set($this->getAuthorizeRequestName(), $authorizeRequest);
    }*/
    
    /**
     * 移除授权请求。
     * 
     * @return AuthorizeRequestInterface
     
    public function removeAuthorizeRequest()
    {
        return Yii::$app->getSession()->remove($this->getAuthorizeRequestName());
    }*/
}