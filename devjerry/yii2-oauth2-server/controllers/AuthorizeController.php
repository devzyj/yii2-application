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
            ],
            'login' => [
                'class' => LoginAction::class,
                'modelClass' => $module->loginFormClass,
            ],
            'authorization' => [
                'class' => AuthorizationAction::class,
                'modelClass' => $module->authorizationFormClass,
            ],
        ];
    }
}
