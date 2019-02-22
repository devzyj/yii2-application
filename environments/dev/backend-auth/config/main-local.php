<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
 
//use yii\web\Response;

/**
 * dev configuration.
 */
$config = [
    /*'bootstrap' => [
        'contentNegotiator' => [
            'formats' => [
                'text/html' => Response::FORMAT_HTML,
            ],
        ],
    ],*/
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/app-dev.log',
                ],
            ],
        ],
        'urlManager' => [
            'rules' => [
                '/oauth2/demo/login' => '/oauth2/demo/login',
                '/oauth2/demo/authorization' => '/oauth2/demo/authorization',
                '/oauth2/demo/logout' => '/oauth2/demo/logout',
            ],
        ],
    ],
    'modules' => [
        'oauth2' => [
            'user' => [
                'class' => 'yii\web\User',
                'identityClass' => 'backendAuth\models\DemoUserIdentity',
            ],
            'loginUrl' => ['/oauth2/demo/login'],
            'authorizationUrl' => ['/oauth2/demo/authorization'],
            'controllerMap' => [
                'demo' => 'devzyj\yii2\oauth2\server\demos\controllers\DemoController',
            ],
            'classMap' => [
                'devzyj\yii2\oauth2\server\demos\models\DemoLoginForm' => 'backendAuth\models\DemoLoginForm',
                'devzyj\yii2\oauth2\server\demos\models\DemoAuthorizationForm' => 'backendAuth\models\DemoAuthorizationForm',
            ],
        ],
    ],
];

return $config;
