<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

$config = [
    'id' => 'app-backend-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backendApi\controllers',
    'bootstrap' => ['log', 'oauth2', 'v1'],
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
        'response' => [
            // 设置判断是否始终使用 `200` 作为 HTTP 状态，并将实际的 HTTP 状态码包含在响应内容中。
            'as suppressResponseCodeBehavior' => [
                'class' => '\devzyj\rest\behaviors\SuppressResponseCodeBehavior',
                'suppressResponseCodeParam' => 'suppress_response_code'
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
        /*'oauthUser' => [
            'class' => 'yii\web\User',
            'identityClass' => 'devzyj\yii2\oauth2\server\demos\models\DemoUserIdentity',
        ],*/
    ],
    'modules' => [
        'oauth2' => [
            'class' => 'devzyj\yii2\oauth2\server\Module',
            'accessTokenCryptKey' => [
                'privateKey' => '@backendApi/config/keys/access-token-private.key',
                'publicKey' => '@backendApi/config/keys/access-token-public.key',
            ],
            'authorizationCodeCryptKey' => [
                'path' => '@backendApi/config/keys/authorization-code-ascii.txt',
            ],
            'refreshTokenCryptKey' => [
                'path' => '@backendApi/config/keys/refresh-token-ascii.txt',
            ],
            'defaultScopes' => ['basic'],
            'validateAccessTokenQueryParam' => 'access-token',
            // demo config.
            'controllerMap' => [
                'demo' => 'devzyj\yii2\oauth2\server\demos\controllers\DemoController',
            ],
            'loginUrl' => ['/oauth2/demo/login'],
            'authorizationUrl' => ['/oauth2/demo/authorization'],
            'user' => [
                'class' => 'yii\web\User',
                'identityClass' => 'devzyj\yii2\oauth2\server\demos\models\DemoUserIdentity',
            ],
            'userRepositoryClass' => 'devzyj\yii2\oauth2\server\demos\models\DemoUserRepository',
            'classMap' => [
                '\devzyj\yii2\oauth2\server\entities\ClientEntity' => '\app\models\ClientEntityaaa',
            ],
        ],
        'v1' => [
            'class' => 'backendApiV1\Module',
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