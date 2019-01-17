<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use devzyj\oauth2\server\AuthorizationServer;
use devzyj\oauth2\server\exceptions\OAuthServerException;
use devjerry\yii2\oauth2\server\ServerRequest;
use devjerry\yii2\oauth2\server\repositories\AccessTokenRepository;
use devjerry\yii2\oauth2\server\repositories\AuthorizationCodeRepository;
use devjerry\yii2\oauth2\server\repositories\ClientRepository;
use devjerry\yii2\oauth2\server\repositories\RefreshTokenRepository;
use devjerry\yii2\oauth2\server\repositories\ScopeRepository;

/**
 * TokenAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class TokenAction extends \yii\base\Action
{
    /**
     * @return array
     */
    public function run()
    {
        // 创建授权服务器实例。
        $authorizationServer = $this->getAuthorizationServer();

        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->controller->module;
        
        // 服务器请求实例。
        $serverRequest = Yii::createObject(ServerRequest::class);
        $serverRequest->parsers = ArrayHelper::merge([
            'application/json' => 'yii\web\JsonParser',
        ], $serverRequest->parsers);
        
        try {
            // 运行并获取授予的认证信息。
            return $authorizationServer->runGrantTypes($serverRequest);
        } catch (OAuthServerException $e) {
            throw new HttpException($e->getHttpStatusCode(), $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * 获取授权服务器实例。
     * 
     * @return AuthorizationServer
     */
    protected function getAuthorizationServer()
    {
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->controller->module;
        
        // 实例化对像。
        $authorizationServer = Yii::createObject([
            'class' => AuthorizationServer::class,
            'accessTokenRepository' => Yii::createObject(AccessTokenRepository::class),
            'authorizationCodeRepository' => Yii::createObject(AuthorizationCodeRepository::class),
            'clientRepository' => Yii::createObject(ClientRepository::class),
            'refreshTokenRepository' => Yii::createObject(RefreshTokenRepository::class),
            'scopeRepository' => Yii::createObject(ScopeRepository::class),
            'userRepository' => Yii::createObject($module->userRepositoryClass),
            'defaultScopes' => $module->defaultScopes,
            'accessTokenDuration' => $module->accessTokenDuration,
            'accessTokenCryptKey' => $module->accessTokenCryptKey,
            'authorizationCodeCryptKey' => $module->authorizationCodeCryptKey,
            'refreshTokenDuration' => $module->refreshTokenDuration,
            'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
        ]);

        // 添加授予类型。
        foreach ($module->grantTypeClasses as $grantTypeClass) {
            $authorizationServer->addGrantType(Yii::createObject($grantTypeClass));
        }
        
        // 返回对像。
        return $authorizationServer;
    }
}