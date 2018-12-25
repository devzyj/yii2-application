<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\controllers;

use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use devzyj\oauth2\server\AuthorizationServer;
use devzyj\oauth2\server\exceptions\OAuthServerException;

/**
 * TokenController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class TokenController extends \yii\web\Controller
{
    /**
     * @var \devjerry\yii2\oauth2\server\Module 控制器所属的模块。
     */
    public $module;
    
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
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
        ];
    }
    
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
        ];
    }
    
    /**
     * @return array
     */
    public function actionIndex()
    {
        // 创建授权服务器实例。
        $authorizationServer = $this->createAuthorizationServer();
        
        // 添加授予类型。
        foreach ($this->module->grantTypes as $grantType) {
            $authorizationServer->addGrantType(Yii::createObject($grantType));
        }
        
        try {
            // 运行并获取授予的认证信息。
            return $authorizationServer->runGrantTypes($this->getServerRequest());
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
            'refreshTokenRepository' => Yii::createObject($this->module->refreshTokenRepository),
            'scopeRepository' => Yii::createObject($this->module->scopeRepository),
            'userRepository' => Yii::createObject($this->module->userRepository),
            'defaultScopes' => $this->module->defaultScopes,
            'accessTokenDuration' => $this->module->accessTokenDuration,
            'accessTokenCryptKey' => $this->module->accessTokenCryptKey,
            'authorizationCodeCryptKey' => $this->module->authorizationCodeCryptKey,
            'refreshTokenDuration' => $this->module->refreshTokenDuration,
            'refreshTokenCryptKey' => $this->module->refreshTokenCryptKey,
        ]);
    }
    
    /**
     * 获取服务器请求实例。
     * 
     * @return \yii\web\Request
     */
    protected function getServerRequest()
    {
        $serverRequest = Yii::createObject($this->module->serverRequest);
        $serverRequest->parsers = ArrayHelper::merge([
            'application/json' => 'yii\web\JsonParser',
        ], $serverRequest->parsers);
        
        return $serverRequest;
    }
}
