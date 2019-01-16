<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use yii\web\HttpException;
use yii\base\InvalidConfigException;
use devzyj\oauth2\server\exceptions\OAuthServerException;
use devjerry\yii2\oauth2\server\interfaces\OAuthIdentityInterface;
use devzyj\oauth2\server\AuthorizationServer;

/**
 * AuthorizeAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizeAction extends \yii\base\Action
{
    /**
     * 用户授权。
     */
    public function run()
    {
        // 创建授权服务器实例。
        $authorizationServer = $this->getAuthorizationServer();

        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->controller->module;
        
        // 服务器请求实例。
        $serverRequest = Yii::createObject($module->serverRequestClass);
        
        try {
            // 获取并验证授权请求。
            $authorizeRequest = $authorizationServer->getAuthorizeRequest($serverRequest);
            
            // 获取授权用户。
            $user = $module->getUser();
            //$user->logout();
            
            // 判断用户是否登录。
            if ($user->getIsGuest()) {
                // 保存授权请求对像。
                $module->setAuthorizeRequest($authorizeRequest);
                
                // 设置回调地址。
                $user->setReturnUrl(Yii::$app->getRequest()->getUrl());

                // 用户未登录，重定向到登录页面。
                return $this->controller->redirect($module->loginUrl);
            }
            
            // 已登录的授权用户。
            $userIdentity = $user->getIdentity();
            if (!$userIdentity instanceof OAuthIdentityInterface) {
                throw new InvalidConfigException('The `user` does not implement OAuthIdentityInterface.');
            }

            // 判断用户是否已确认授权。
            $isApproved = $userIdentity->getOAuthIsApproved();
            if ($isApproved === null) {
                // 保存授权请求对像。
                $module->setAuthorizeRequest($authorizeRequest);
                
                // 设置回调地址。
                $user->setReturnUrl(Yii::$app->getRequest()->getUrl());

                // 用户未确认是否授权，重定向到授权页面。
                return $this->controller->redirect($module->authorizationUrl);
            }

            // 设置用户是否同意授权的状态为 `null`。
            $userIdentity->removeOAuthIsApproved();
            
            // 设置运行授权时的参数。
            $authorizeRequest->setUserEntity($userIdentity->getOAuthUserEntity());
            $authorizeRequest->setIsApproved($isApproved);
            $scopeEntities = $userIdentity->getOAuthScopeEntities();
            if ($scopeEntities !== null) {
                $authorizeRequest->setScopeEntities($scopeEntities);
            }
            
            // 运行并返回授权成功的回调地址。
            $redirectUri = $authorizationServer->runAuthorizeTypes($authorizeRequest);

            // 移除保存的授权请求对像。
            $module->removeAuthorizeRequest();
            
            // 重定向到授权成功的回调地址。
            return $this->controller->redirect($redirectUri);
        } catch (OAuthServerException $e) {
            // 移除保存的授权请求对像。
            $module->removeAuthorizeRequest();
            
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
        
        // 返回对像。
        return $authorizationServer;
    }
}