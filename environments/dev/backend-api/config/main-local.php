<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\web\Response;

/**
 * dev configuration.
 */
$config = [
    'bootstrap' => [
        'contentNegotiator' => [
            'formats' => [
                'text/html' => Response::FORMAT_HTML,
            ],
        ],
    ],
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
                '/' => 'site/index',
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
                'identityClass' => 'backendApi\models\oauth2\DemoUserIdentity',
            ],
            'loginUrl' => ['/oauth2/demo/login'],
            'authorizationUrl' => ['/oauth2/demo/authorization'],
            'controllerMap' => [
                'demo' => 'devzyj\yii2\oauth2\server\demos\controllers\DemoController',
            ],
            'classMap' => [
                'devzyj\yii2\oauth2\server\demos\models\DemoLoginForm' => 'backendApi\models\oauth2\DemoLoginForm',
                'devzyj\yii2\oauth2\server\demos\models\DemoAuthorizationForm' => 'backendApi\models\oauth2\DemoAuthorizationForm',
                'devzyj\oauth2\server\authorizes\CodeAuthorize' => [
                    'class' => 'devzyj\oauth2\server\authorizes\CodeAuthorize',
                    'enableCodeChallenge' => true,
                    'defaultCodeChallengeMethod' => 'S256',
                ],
                'devzyj\oauth2\server\grants\AuthorizationCodeGrant' => [
                    'class' => 'devzyj\oauth2\server\grants\AuthorizationCodeGrant',
                    'enableCodeChallenge' => true,
                ],
            ],
        ],
    ],
];

return $config;
