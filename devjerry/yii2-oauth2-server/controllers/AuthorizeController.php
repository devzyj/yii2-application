<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\controllers;

use Yii;
use yii\web\User;
use yii\web\HttpException;
use yii\base\InvalidConfigException;
use devzyj\oauth2\server\AuthorizationServer;
use devzyj\oauth2\server\authorizes\AuthorizeRequestInterface;
use devzyj\oauth2\server\exceptions\OAuthServerException;
use devjerry\yii2\oauth2\server\entities\UserEntity;
use devjerry\yii2\oauth2\server\interfaces\UserIdentityInterface;

/**
 * AuthorizeController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizeController extends \yii\web\Controller
{
    const AUTHORIZE_REQUEST_NAME = 'OAUTH2_AUTHORIZE_REQUEST';
    
    /**
     * @var \devjerry\yii2\oauth2\server\Module 控制器所属的模块。
     */
    public $module;
    
    /**
     * @todo 验证用户是否登录，并且引导用户登录。
     * @todo 引导登录后的用户去授权确认页面，并且确认授权。
     */
    public function actionIndex()
    {
        // 创建授权服务器实例。
        $authorizationServer = $this->createAuthorizationServer();

        // 添加授权类型。
        foreach ($this->module->authorizeTypes as $authorizeType) {
            $authorizationServer->addAuthorizeType(Yii::createObject($authorizeType));
        }
        
        try {
            // 获取并验证授权请求。
            $authorizeRequest = $authorizationServer->getAuthorizeRequest($this->getServerRequest());
            
            // 判断用户是否登录，并且设置。
            if ($authorizeRequest->getUsertEntity() === null) {
                // 设置回调地址。
                $this->setReturnUrl($this->getReturnUrl());
                
                // 重定向到授权确认页面。
                $this->redirect($this->module->authorizeUrl);
            }
            
            // 获取保存在 SESSION 中的授权请求。
            $sessionAuthorizeRequest = $this->getAuthorizeRequest();
            
            
            
            if (Yii::$app->getRequest()->getIsPost()) {
                
            }
            
            
            
            
            
            
            
            // 获取保存在 SESSION 中的授权请求。
            $authorizeRequest = $this->getAuthorizeRequest();
            if ($authorizeRequest === null) {
                // 获取并验证授权请求。
                $authorizeRequest = $authorizationServer->getAuthorizeRequest($this->getServerRequest());
                
                // 保存授权请求到 SESSION 中。
                $this->setAuthorizeRequest($authorizeRequest);
            }
            
            // 判断是否设置了授权用户。
            if ($authorizeRequest->getUsertEntity() === null) {
                // 获取授权用户。
                $user = $this->getUser();
                if ($user->getIsGuest()) {
                    // 重定向到登录页面。
                    $user->loginRequired();
                }
                
                // 设置授权用户。
                $authorizeRequest->setUserEntity($user->getIdentity());
            }
            
            // 判断是否设置了授权状态。
            if ($authorizeRequest->getIsApproved() === null) {
                // 设置回调地址。
                $this->setReturnUrl(Yii::$app->getRequest()->getUrl());
                
                // 重定向到授权确认页面。
                $this->redirect($this->module->authorizeUrl);
            }
            
            
            // 获取授权用户。
            $user = $this->getUser();
            if ($user->getIsGuest()) {
                // 重定向到登录页面。
                $user->loginRequired();
            }
            
            // 获取登录的用户实例。
            $userEntity = $user->getIdentity();
            
            
            
            // 设置授权的用户。
            $user = new UserEntity();
            $user->id = 1;
            $user->username = 'jerry';
            $authorizeRequest->setUserEntity($user);

            // 设置是否同意授权。
            $authorizeRequest->setIsApproved(true);
            
            // 运行并返回授权成功的回调地址。
            $redirectUri = $authorizationServer->runAuthorizeTypes($authorizeRequest);

            $this->setAuthorizeRequest(null);
        } catch (OAuthServerException $e) {
            $this->setAuthorizeRequest(null);
            throw new HttpException($e->getHttpStatusCode(), $e->getMessage(), $e->getCode(), $e);
        }
        
        // 重定向到回调地址。
        $this->redirect($redirectUri);
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
     * 获取服务器请求实例。
     * 
     * @return \yii\web\Request
     */
    protected function getServerRequest()
    {
        return Yii::createObject($this->module->serverRequest);
    }
    
    /**
     * 获取保存在 SESSION 中的授权请求。
     * 
     * @return AuthorizeRequestInterface 
     */
    public function getAuthorizeRequest()
    {
        return Yii::$app->getSession()->get(self::AUTHORIZE_REQUEST_NAME);
    }
    
    /**
     * 保存授权请求到 SESSION 中。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest
     */
    public function setAuthorizeRequest($authorizeRequest)
    {
        Yii::$app->getSession()->set(self::AUTHORIZE_REQUEST_NAME, $authorizeRequest);
    }
    
    /**
     * 确认授权请求。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest
     * @return AuthorizeRequestInterface
     */
    protected function ensureAuthorizeRequest($authorizeRequest)
    {
        $sessionAuthorizeRequest = $this->getAuthorizeRequest();
        if ($sessionAuthorizeRequest->getAuthorizeType()->getIdentifier() !== $authorizeRequest->getAuthorizeType()->getIdentifier()) {
            return $authorizeRequest;
        } elseif ($sessionAuthorizeRequest->getClientEntity()->getIdentifier() !== $authorizeRequest->getClientEntity()->getIdentifier()) {
            return $authorizeRequest;
        } elseif ($sessionAuthorizeRequest->getRedirectUri() !== $authorizeRequest->getRedirectUri()) {
            return $authorizeRequest;
        } elseif ($sessionAuthorizeRequest->getState()) {
            
        }
    }
    
    /**
     * 获取登录，确认授权后的回调地址。
     * 
     * @return string
     */
    protected function getReturnUrl()
    {
        return Yii::$app->getRequest()->getUrl();
    }
    
    /**
     * 获取授权用户。
     * 
     * @return User
     */
    protected function getUser()
    {
        if (!$this->module->user instanceof User) {
            throw new InvalidConfigException('The `user` property must be set.');
        }
        
        return $this->module->user;
    }
}
