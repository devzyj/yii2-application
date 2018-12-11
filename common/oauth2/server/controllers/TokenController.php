<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\web\BadRequestHttpException;

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
        /* @var $module \common\oauth2\server\Module */
        $module = $this->module;
        
        return [
            // authorization_code
            'authorization-code' => [
                'class' => 'common\oauth2\server\actions\AuthorizationCodeGrantAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
            ],
            // password
            'user-credentials' => [
                'class' => 'common\oauth2\server\actions\PasswordGrantAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
            ],
            // client_credentials
            'client-credentials' => [
                'class' => 'common\oauth2\server\actions\ClientCredentialsGrantAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
            ],
            // refresh_token
            'refresh-token' => [
                'class' => 'common\oauth2\server\actions\RefreshTokenGrantAction',
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
            'authorization-code' => ['POST'],
            'user-credentials' => ['POST'],
            'client-credentials' => ['POST'],
            'refresh-token' => ['POST'],
        ];
    }
}
