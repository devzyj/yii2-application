<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\controllers;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * AuthorizeController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizeController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        /* @var $module \common\oauth2\server\Module */
        $module = $this->module;
        
        return [
            // code
            'code' => [
                'class' => 'common\oauth2\server\actions\CodeAuthorizeAction',
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'refreshTokenCryptKey' => $module->refreshTokenCryptKey,
            ],
            // token
            'token' => [
                'class' => 'common\oauth2\server\actions\TokenAuthorizeAction',
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
        $responseType = Yii::$app->getRequest()->getQueryParam('response_type');
        if (empty($responseType)) {
            throw new BadRequestHttpException('Missing parameters: "response_type" required.');
        }
        
        if ($responseType === 'code') {
            return $this->runAction('code');
        } elseif ($responseType === 'token') {
            return $this->runAction('token');
        }
    }
}
