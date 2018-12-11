<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use Yii;

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
     * @var mixed 授权码密钥。
     * @see \devjerry\oauth2\server\interfaces\AuthCodeRepositoryInterface::serializeAuthCode()
     * @see \devjerry\oauth2\server\interfaces\AuthCodeRepositoryInterface::unserializeAuthCode()
     */
    public $authCodeCryptKey;
    
    /**
     * @var mixed 访问令牌密钥。
     * @see \devjerry\oauth2\server\interfaces\AccessTokenRepositoryInterface::serializeAccessToken()
     * @see \devjerry\oauth2\server\interfaces\AccessTokenRepositoryInterface::unserializeAccessToken()
     */
    public $accessTokenCryptKey;

    /**
     * @var mixed 更新令牌密钥。
     * @see \devjerry\oauth2\server\interfaces\RefreshTokenRepositoryInterface::serializeRefreshToken()
     * @see \devjerry\oauth2\server\interfaces\RefreshTokenRepositoryInterface::unserializeRefreshToken()
     */
    public $refreshTokenCryptKey;
    
    /**
     * @var array
     
    public $entityClassMap = [
        'AccessTokenEntity' => 'devjerry\yii2\oauth2\server\entities\AccessTokenEntity',
        'AuthCodeEntity' => 'devjerry\yii2\oauth2\server\entities\AuthCodeEntity',
        'ClientEntity' => 'devjerry\yii2\oauth2\server\entities\ClientEntity',
        'RefreshTokenEntity' => 'devjerry\yii2\oauth2\server\entities\RefreshTokenEntity',
        'ScopeEntity' => 'devjerry\yii2\oauth2\server\entities\ScopeEntity',
        'UserEntity' => 'devjerry\yii2\oauth2\server\entities\UserEntity',
    ];*/
    
    /**
     * @var array
     
    public $repositoryClassMap = [
        'AccessTokenRepository' => 'devjerry\yii2\oauth2\server\repositories\AccessTokenRepository',
        'AuthCodeRepository' => 'devjerry\yii2\oauth2\server\repositories\AuthCodeRepository',
        'ClientRepository' => 'devjerry\yii2\oauth2\server\repositories\ClientRepository',
        'RefreshTokenRepository' => 'devjerry\yii2\oauth2\server\repositories\RefreshTokenRepository',
        'ScopeRepository' => 'devjerry\yii2\oauth2\server\repositories\ScopeRepository',
        'UserRepository' => 'devjerry\yii2\oauth2\server\repositories\UserRepository',
    ];*/
    
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