<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use devjerry\oauth2\server\AuthorizationServer;
use devjerry\oauth2\server\grants\ClientCredentialsGrant;
use devjerry\yii2\oauth2\server\repositories\AccessTokenRepository;
use devjerry\yii2\oauth2\server\repositories\AuthorizationCodeRepository;
use devjerry\yii2\oauth2\server\repositories\ClientRepository;
use devjerry\yii2\oauth2\server\repositories\RefreshTokenRepository;
use devjerry\yii2\oauth2\server\repositories\ScopeRepository;
use devjerry\yii2\oauth2\server\repositories\UserRepository;
use devjerry\yii2\oauth2\server\ServerRequest;
use devjerry\oauth2\server\grants\PasswordGrant;
use devjerry\oauth2\server\grants\RefreshTokenGrant;

/**
 * TokenController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class TokenController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => $this->verbs(),
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->module;
        
        return [
            // authorization_code
            'authorization-code' => [
                'class' => 'devjerry\yii2\oauth2\server\actions\AuthorizationCodeGrantAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
            ],
            // password
            'user-credentials' => [
                'class' => 'devjerry\yii2\oauth2\server\actions\PasswordGrantAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
            ],
            // client_credentials
            'client-credentials' => [
                'class' => 'devjerry\yii2\oauth2\server\actions\ClientCredentialsGrantAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
            ],
            // refresh_token
            'refresh-token' => [
                'class' => 'devjerry\yii2\oauth2\server\actions\RefreshTokenGrantAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
            ],
        ];
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->module;
        
        /* @var $authorizeServer AuthorizationServer */
        $authorizeServer = Yii::createObject(AuthorizationServer::class);

        /* @var $accessTokenRepository AccessTokenRepository */
        $accessTokenRepository = Yii::createObject(AccessTokenRepository::class);

        /* @var $authorizationCodeRepository AuthorizationCodeRepository */
        //$authorizationCodeRepository = Yii::createObject(AuthorizationCodeRepository::class);

        /* @var $clientRepository ClientRepository */
        $clientRepository = Yii::createObject(ClientRepository::class);

        /* @var $refreshTokenRepository RefreshTokenRepository */
        $refreshTokenRepository = Yii::createObject(RefreshTokenRepository::class);

        /* @var $scopeRepository ScopeRepository */
        $scopeRepository = Yii::createObject(ScopeRepository::class);

        /* @var $userRepository UserRepository */
        $userRepository = Yii::createObject(UserRepository::class);
        
        /* @var $clientCredentialsGrant ClientCredentialsGrant */
        $clientCredentialsGrant = Yii::createObject(ClientCredentialsGrant::class);
        $clientCredentialsGrant->setAccessTokenCryptKey($module->accessTokenCryptKey);
        $clientCredentialsGrant->setAccessTokenRepository($accessTokenRepository);
        //$clientCredentialsGrant->setAuthorizationCodeRepository($authorizationCodeRepository);
        $clientCredentialsGrant->setClientRepository($clientRepository);
        $clientCredentialsGrant->setScopeRepository($scopeRepository);
        $authorizeServer->addGrantType($clientCredentialsGrant);

        /* @var $passwordGrant PasswordGrant */
        $passwordGrant = Yii::createObject(PasswordGrant::class);
        $passwordGrant->setAccessTokenCryptKey($module->accessTokenCryptKey);
        $passwordGrant->setRefreshTokenCryptKey($module->refreshTokenCryptKey);
        $passwordGrant->setAccessTokenRepository($accessTokenRepository);
        $passwordGrant->setClientRepository($clientRepository);
        $passwordGrant->setRefreshTokenRepository($refreshTokenRepository);
        $passwordGrant->setScopeRepository($scopeRepository);
        $passwordGrant->setUserRepository($userRepository);
        $authorizeServer->addGrantType($passwordGrant);

        /* @var $refreshTokenGrant RefreshTokenGrant */
        $refreshTokenGrant = Yii::createObject(RefreshTokenGrant::class);
        $refreshTokenGrant->setAccessTokenCryptKey($module->accessTokenCryptKey);
        $refreshTokenGrant->setRefreshTokenCryptKey($module->refreshTokenCryptKey);
        $refreshTokenGrant->setAccessTokenRepository($accessTokenRepository);
        $refreshTokenGrant->setClientRepository($clientRepository);
        $refreshTokenGrant->setRefreshTokenRepository($refreshTokenRepository);
        $refreshTokenGrant->setScopeRepository($scopeRepository);
        $refreshTokenGrant->setUserRepository($userRepository);
        $authorizeServer->addGrantType($refreshTokenGrant);
        
        /* @var $serverRequest ServerRequest */
        $serverRequest = Yii::createObject([
            'class' => ServerRequest::class,
            'enableCsrfValidation' => false,
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ]);
        
        return $authorizeServer->runGrantTypes($serverRequest);
    }
    
    /**
     * @return array
     
    public function actionIndex()
    {
        $grantType = Yii::$app->getRequest()->getBodyParam('grant_type');
        if (empty($grantType)) {
            throw new BadRequestHttpException('Missing parameters: "grant_type" required.');
        }
        
        // run actions
        if ($grantType === 'authorization_code') {
            return $this->runAction('authorization-code');
        } elseif ($grantType === 'password') {
            return $this->runAction('user-credentials');
        } elseif ($grantType === 'client_credentials') {
            return $this->runAction('client-credentials');
        } elseif ($grantType === 'refresh_token') {
            return $this->runAction('refresh-token');
        }
    }*/

    /**
     * Declares the allowed HTTP verbs.
     * Please refer to [[VerbFilter::actions]] on how to declare the allowed verbs.
     *
     * @return array the allowed HTTP verbs.
     */
    protected function verbs()
    {
        return [
            'index' => ['POST'],
            'authorization-code' => ['POST'],
            'user-credentials' => ['POST'],
            'client-credentials' => ['POST'],
            'refresh-token' => ['POST'],
        ];
    }
}
