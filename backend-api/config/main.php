<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\web\Response;

$config = [
    'id' => 'app-backend-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backendApi\controllers',
    'bootstrap' => [
        'log', 
        'contentNegotiator' => [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ],
        'oauth2',
        'v1',
    ],
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
                'suppressResponseCodeParam' => 'suppress_response_code',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
    ],
    'modules' => [
        'oauth2' => [
            'class' => 'devzyj\yii2\oauth2\server\Module',
            'db' => 'db_backend',
            'accessTokenCryptKey' => [
                'privateKey' => '@backendApi/config/oauth2-keys/access-token-private.key',
                'publicKey' => '@backendApi/config/oauth2-keys/access-token-public.key',
            ],
            'authorizationCodeCryptKey' => [
                'path' => '@backendApi/config/oauth2-keys/authorization-code-ascii.txt',
            ],
            'refreshTokenCryptKey' => [
                'path' => '@backendApi/config/oauth2-keys/refresh-token-ascii.txt',
            ],
            'defaultScopes' => ['basic'],
            'validateAccessTokenQueryParam' => 'access-token',
            'userRepositoryClass' => 'backendApi\models\oauth2\UserRepository',
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