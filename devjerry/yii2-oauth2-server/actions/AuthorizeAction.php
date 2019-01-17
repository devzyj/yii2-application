<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\HttpException;
use yii\helpers\Url;
use devzyj\oauth2\server\AuthorizationServer;
use devzyj\oauth2\server\authorizes\AuthorizeRequestInterface;
use devzyj\oauth2\server\interfaces\ScopeEntityInterface;
use devzyj\oauth2\server\exceptions\OAuthServerException;
use devjerry\yii2\oauth2\server\ServerRequest;
use devjerry\yii2\oauth2\server\repositories\AccessTokenRepository;
use devjerry\yii2\oauth2\server\repositories\AuthorizationCodeRepository;
use devjerry\yii2\oauth2\server\repositories\ClientRepository;
use devjerry\yii2\oauth2\server\repositories\ScopeRepository;
use devjerry\yii2\oauth2\server\interfaces\OAuthIdentityInterface;

/**
 * AuthorizeAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizeAction extends \yii\base\Action
{
    /**
     * 用户请求授权。
     */
    public function run()
    {
        // 创建授权服务器实例。
        $authorizationServer = $this->getAuthorizationServer();

        // 服务器请求实例。
        $serverRequest = Yii::createObject(ServerRequest::class);
        
        try {
            // 获取并验证授权请求。
            $authorizeRequest = $authorizationServer->getAuthorizeRequest($serverRequest);

            /* @var $module \devjerry\yii2\oauth2\server\Module */
            $module = $this->controller->module;
            
            // 获取授权用户。
            $user = $module->getUser();
            
            // 判断用户是否登录。
            if ($user->getIsGuest()) {
                // 用户未登录，重定向到登录页面。
                return $this->controller->redirect($this->makeLoginUrl($authorizeRequest));
            }
            
            // 已登录的授权用户。
            $userIdentity = $user->getIdentity();
            if (!$userIdentity instanceof OAuthIdentityInterface) {
                throw new InvalidConfigException('The `User::identity` does not implement OAuthIdentityInterface.');
            }

            // 判断用户是否已确认授权。
            $isApproved = $userIdentity->getOAuthIsApproved();
            if ($isApproved === null) {
                // 用户未确认是否授权，重定向到授权页面。
                return $this->controller->redirect($this->makeAuthorizationUrl($authorizeRequest));
            }

            // 设置用户是否同意授权的状态为 `null`，保证每次都需要用户确认。
            $userIdentity->setOAuthIsApproved(null);
            
            // 设置运行授权时的参数。
            $authorizeRequest->setUserEntity($userIdentity->getOAuthUserEntity());
            $authorizeRequest->setIsApproved($isApproved);
            $scopeEntities = $userIdentity->getOAuthScopeEntities();
            if ($scopeEntities !== null) {
                $authorizeRequest->setScopeEntities($scopeEntities);
            }
            
            // 运行并返回授权成功的回调地址。
            $redirectUri = $authorizationServer->runAuthorizeTypes($authorizeRequest);

            // 重定向到授权成功的回调地址。
            return $this->controller->redirect($redirectUri);
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
            'scopeRepository' => Yii::createObject(ScopeRepository::class),
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
    
    /**
     * 构造用户登录地址。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest
     * @return string
     */
    protected function makeLoginUrl(AuthorizeRequestInterface $authorizeRequest)
    {
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->controller->module;
        return $this->makeUrl($module->loginUrl, $authorizeRequest);
    }
    
    /**
     * 构造用户确认授权地址。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest
     * @return string
     */
    protected function makeAuthorizationUrl(AuthorizeRequestInterface $authorizeRequest)
    {
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->controller->module;
        return $this->makeUrl($module->authorizationUrl, $authorizeRequest);
    }

    /**
     * 构造 URL。
     *
     * @param string|array $url
     * @param AuthorizeRequestInterface $authorizeRequest
     * @return string
     */
    protected function makeUrl($url, AuthorizeRequestInterface $authorizeRequest)
    {
        $params['client_id'] = $authorizeRequest->getClientEntity()->getIdentifier();
    
        $scopeEntities = $authorizeRequest->getScopeEntities();
        if ($scopeEntities) {
            $params['scope'] = implode(' ', array_map(function (ScopeEntityInterface $scopeEntity) {
                return $scopeEntity->getIdentifier();
            }, $scopeEntities));
        }
        
        $params['return_url'] = Yii::$app->getRequest()->getAbsoluteUrl();
        $params['referrer_url'] = Yii::$app->getRequest()->getReferrer();

        $url = Url::to($url);
        if (strpos($url, '?') === false) {
            return $url . '?' . http_build_query($params);
        } else {
            return $url . '&' . http_build_query($params);
        }
    }
}