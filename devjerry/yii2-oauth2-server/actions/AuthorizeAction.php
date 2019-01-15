<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\User;
use yii\web\HttpException;
use devzyj\oauth2\server\AuthorizationServer;
use devzyj\oauth2\server\exceptions\OAuthServerException;

/**
 * AuthorizeAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizeAction extends \yii\base\Action
{
    /**
     * @var User 授权用户。
     */
    public $user;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if ($this->user === null) {
            throw new InvalidConfigException('The `user` property must be set.');
        }
    }
    
    /**
     * @return array
     */
    public function run()
    {
        $controller = $this->controller;
        
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $controller->module;
        
        // 创建授权服务器实例。
        /* @var $authorizationServer AuthorizationServer */
        $authorizationServer = Yii::createObject([
            'class' => $module->authorizationServerClass,
            'accessTokenRepository' => Yii::createObject($module->accessTokenRepositoryClass),
            'authorizationCodeRepository' => Yii::createObject($module->authorizationCodeRepositoryClass),
            'clientRepository' => Yii::createObject($module->clientRepositoryClass),
            'scopeRepository' => Yii::createObject($module->scopeRepositoryClass),
            'userRepository' => Yii::createObject($module->userRepositoryClass),
            'defaultScopes' => $module->defaultScopes,
            'accessTokenDuration' => $module->accessTokenDuration,
            'accessTokenCryptKey' => $module->accessTokenCryptKey,
            'authorizationCodeDuration' => $module->authorizationCodeDuration,
            'authorizationCodeCryptKey' => $module->authorizationCodeCryptKey,
        ]);

        // 添加授权类型。
        foreach ($module->authorizeTypeClasses as $authorizeTypeClass) {
            $authorizationServer->addAuthorizeType(Yii::createObject($authorizeTypeClass));
        }
        
        try {
            // 服务器请求实例。
            $serverRequest = Yii::createObject($module->serverRequestClass);
            
            // 获取并验证授权请求。
            $authorizeRequest = $authorizationServer->getAuthorizeRequest($serverRequest);
            
            // 获取授权用户。
            $user = $this->user;
            if ($user->getIsGuest()) {
                // 设置回调地址。
                $user->setReturnUrl(Yii::$app->getRequest()->getUrl());
            
                // 重定向到登录页面。
                $redirectUri = $module->loginUrl;
            } elseif ($authorizeRequest->getIsApproved() === null) {
                // 设置回调地址。
                $user->setReturnUrl(Yii::$app->getRequest()->getUrl());
                
                // 重定向到授权页面。
                $redirectUri = $module->authorizationUrl;
            } else {
                // 运行并返回授权成功的回调地址。
                $redirectUri = $authorizationServer->runAuthorizeTypes($authorizeRequest);
            }
        } catch (OAuthServerException $e) {
            throw new HttpException($e->getHttpStatusCode(), $e->getMessage(), $e->getCode(), $e);
        }
        
        // 重定向页面。
        $controller->redirect($redirectUri);
    }
}