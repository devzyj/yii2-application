<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server;

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
    }
}