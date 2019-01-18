<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\controllers;

use devjerry\yii2\oauth2\server\actions\AuthorizeAction;
use devjerry\yii2\oauth2\server\actions\LoginAction;
use devjerry\yii2\oauth2\server\actions\AuthorizationAction;

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
        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->module;
        
        return [
            'index' => [
                'class' => AuthorizeAction::class,
                'authorizeTypeClasses' => $module->authorizeTypeClasses,
                'userRepositoryClass' => $module->userRepositoryClass,
                'defaultScopes' => $module->defaultScopes,
                'accessTokenDuration' => $module->accessTokenDuration,
                'accessTokenCryptKey' => $module->accessTokenCryptKey,
                'authorizationCodeDuration' => $module->authorizationCodeDuration,
                'authorizationCodeCryptKey' => $module->authorizationCodeCryptKey,
                'user' => $module->user,
                'loginUrl' => $module->loginUrl,
                'authorizationUrl' => $module->authorizationUrl,
            ],
            'login' => [
                'class' => LoginAction::class,
                'user' => $module->user,
                'modelClass' => $module->loginFormClass,
                'view' => $module->loginActionView,
                'layout' => $module->loginActionLayout,
                'authorizationUrl' => $module->authorizationUrl,
            ],
            'authorization' => [
                'class' => AuthorizationAction::class,
                'user' => $module->user,
                'modelClass' => $module->authorizationFormClass,
                'view' => $module->authorizationActionView,
                'layout' => $module->authorizationActionLayout,
                'loginUrl' => $module->loginUrl,
            ],
        ];
    }
}
