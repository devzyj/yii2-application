<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server;

use Yii;

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
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    /**
     * @var mixed 授权码密钥。
     * @see \common\oauth2\server\interfaces\AuthCodeRepositoryInterface::serializeAuthCode()
     * @see \common\oauth2\server\interfaces\AuthCodeRepositoryInterface::unserializeAuthCode()
     */
    public $authCodeCryptKey;
    
    /**
     * @var mixed 访问令牌密钥。
     * @see \common\oauth2\server\interfaces\AccessTokenRepositoryInterface::serializeAccessToken()
     * @see \common\oauth2\server\interfaces\AccessTokenRepositoryInterface::unserializeAccessToken()
     */
    public $accessTokenCryptKey;

    /**
     * @var mixed 更新令牌密钥。
     * @see \common\oauth2\server\interfaces\RefreshTokenRepositoryInterface::serializeRefreshToken()
     * @see \common\oauth2\server\interfaces\RefreshTokenRepositoryInterface::unserializeRefreshToken()
     */
    public $refreshTokenCryptKey;
    
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