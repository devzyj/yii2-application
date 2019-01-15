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
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->controller->module;
        
        // 创建授权服务器实例。
        /* @var $authorizationServer AuthorizationServer */
        $authorizationServer = Yii::createObject([
            'class' => $module->authorizationServerClass,
            'accessTokenRepository' => Yii::createObject($module->accessTokenRepositoryClass),
            'authorizationCodeRepository' => Yii::createObject($module->authorizationCodeRepositoryClass),
            'clientRepository' => Yii::createObject($module->clientRepositoryClass),
            'refreshTokenRepository' => Yii::createObject($module->refreshTokenRepositoryClass),
            'scopeRepository' => Yii::createObject($module->scopeRepositoryClass),
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
        
        try {
            // 服务器请求实例。
            $serverRequest = Yii::createObject($module->serverRequestClass);
            $serverRequest->parsers = ArrayHelper::merge([
                'application/json' => 'yii\web\JsonParser',
            ], $serverRequest->parsers);
            
            // 运行并获取授予的认证信息。
            return $authorizationServer->runGrantTypes($serverRequest);
        } catch (OAuthServerException $e) {
            throw new HttpException($e->getHttpStatusCode(), $e->getMessage(), $e->getCode(), $e);
        }
    }
}