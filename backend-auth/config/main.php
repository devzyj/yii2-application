<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\web\Response;

$config = [
    'id' => 'app-backend-auth',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backendAuth\controllers',
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
            'userRepositoryClass' => 'backendAuth\models\UserRepository',
        ],
    ],
];

// application.params
$config['params'] = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return $config;