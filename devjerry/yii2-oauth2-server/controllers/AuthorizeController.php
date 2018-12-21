<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\controllers;

use Yii;
use yii\web\HttpException;
use yii\web\BadRequestHttpException;
use devzyj\oauth2\server\AuthorizationServer;
use devzyj\oauth2\server\exceptions\OAuthServerException;

/**
 * AuthorizeController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizeController extends \yii\web\Controller
{
    /**
     * @var \devjerry\yii2\oauth2\server\Module 控制器所属的模块。
     */
    public $module;
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->module;
        
        return [
            // code
            'code' => [
                'class' => 'devjerry\yii2\oauth2\server\actions\CodeAuthorizeAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
            ],
            // token
            'token' => [
                'class' => 'devjerry\yii2\oauth2\server\actions\TokenAuthorizeAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
            ],
        ];
    }
    
    /**
     * @return array
     * @todo 验证用户是否登录，并且引导用户登录。
     * @todo 引导登录后的用户去授权确认页面，并且确认授权。
     */
    public function actionIndex()
    {
        // 创建授权服务器实例。
        $authorizationServer = $this->createAuthorizationServer();

        // 添加授权类型。
        $this->addAuthorizeTypes($authorizationServer);
        
        try {
            // 获取并验证授权请求。
            $authorizeRequest = $authorizationServer->getAuthorizeRequest($this->getServerRequest());
            
            // 设置授权的用户。
            $authorizeRequest->setUser(new UserEntity());
            
            // 设置同意授权。
            $authorizeRequest->setIsApproved(true);
            
            // 运行并返回授权成功的回调地址。
            $result = $authorizationServer->runAuthorizeTypes($authorizeRequest);
            
            // 根据授权类型，进行不同的处理。
            if ($authorizeRequest->getAuthorizeType()->getIdentifier() === 'code') {
                // 授权码模式，返回值为回调地址。
                $this->redirect($result);
            } else {
                // 简单授权模式，返回值为认证信息。
                return $result;
            }
        } catch (OAuthServerException $e) {
            throw new HttpException($e->getHttpStatusCode(), $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * 创建授权服务器实例。
     * 
     * @return AuthorizationServer
     */
    protected function createAuthorizationServer()
    {
        // 创建并返回授权服务器实例。
        return new AuthorizationServer([
            'accessTokenRepository' => Yii::createObject($this->module->accessTokenRepository),
            'authorizationCodeRepository' => Yii::createObject($this->module->authorizationCodeRepository),
            'clientRepository' => Yii::createObject($this->module->clientRepository),
            'scopeRepository' => Yii::createObject($this->module->scopeRepository),
            'defaultScopes' => $this->module->defaultScopes,
            'accessTokenDuration' => $this->module->accessTokenDuration,
            'accessTokenCryptKey' => $this->module->accessTokenCryptKey,
            'authorizationCodeDuration' => $this->module->authorizationCodeDuration,
            'authorizationCodeCryptKey' => $this->module->authorizationCodeCryptKey,
        ]);
    }
    
    /**
     * 添加授权类型。
     * 
     * @param AuthorizationServer $authorizationServer
     * @return AuthorizationServer
     */
    protected function addAuthorizeTypes($authorizationServer)
    {
        foreach ($this->module->authorizeTypes as $authorizeType) {
            $authorizationServer->addAuthorizeType(Yii::createObject($authorizeType));
        }
        
        return $authorizationServer;
    }
    
    /**
     * 获取服务器请求实例。
     * 
     * @return \yii\web\Request
     */
    protected function getServerRequest()
    {
        return $this->module->serverRequest;
    }
}
