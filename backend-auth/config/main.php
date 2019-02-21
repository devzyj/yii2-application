<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

$config = [
    'id' => 'app-backend-auth',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backendAuth\controllers',
    'bootstrap' => ['log', 'oauth2'],
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request' => [
            'enableCsrfValidation' => false,
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
                '/oauth2/demo/login' => '/oauth2/demo/login',
                '/oauth2/demo/authorization' => '/oauth2/demo/authorization',
                '/oauth2/demo/logout' => '/oauth2/demo/logout',
            ],
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'backendAuth\components\oauth2\UserIdentity',
        ],
    ],
    'modules' => [
        'oauth2' => [
            'class' => 'devzyj\yii2\oauth2\server\Module',
            'accessTokenCryptKey' => [
                'privateKey' => '@backendAuth/config/keys/access-token-private.key',
                'publicKey' => '@backendAuth/config/keys/access-token-public.key',
            ],
            'authorizationCodeCryptKey' => [
                'path' => '@backendAuth/config/keys/authorization-code-ascii.txt',
            ],
            'refreshTokenCryptKey' => [
                'path' => '@backendAuth/config/keys/refresh-token-ascii.txt',
            ],
            'defaultScopes' => ['basic'],
            'validateAccessTokenQueryParam' => 'access-token',
            // user authorize
            'userRepositoryClass' => 'backendAuth\components\oauth2\UserRepository',
            // user authorize page
            'user' => 'oauth2User',
            'loginUrl' => ['/oauth2/demo/login'],
            'authorizationUrl' => ['/oauth2/demo/authorization'],
            'controllerMap' => [
                'demo' => 'devzyj\yii2\oauth2\server\demos\controllers\DemoController',
            ],
            'classMap' => [
                'devzyj\yii2\oauth2\server\demos\models\DemoLoginForm' => 'backendAuth\components\oauth2\DemoLoginForm',
                'devzyj\yii2\oauth2\server\demos\models\DemoAuthorizationForm' => 'backendAuth\components\oauth2\DemoAuthorizationForm',
            ],
            // demo config.
            /*'userRepositoryClass' => 'devzyj\yii2\oauth2\server\demos\models\DemoUserRepository',
            'user' => [
                'class' => 'yii\web\User',
                'identityClass' => 'devzyj\yii2\oauth2\server\demos\models\DemoUserIdentity',
            ],
            'loginUrl' => ['/oauth2/demo/login'],
            'authorizationUrl' => ['/oauth2/demo/authorization'],
            'controllerMap' => [
                'demo' => 'devzyj\yii2\oauth2\server\demos\controllers\DemoController',
            ],*/
        ],
    ]
];

// application.params
$config['params'] = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return $config;