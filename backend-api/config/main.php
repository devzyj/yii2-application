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
    'bootstrap' => ['log', 'oauth', 'v1', 'oauth2'],
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
                '/' => 'site/index'
            ],
        ],
    ],
    'modules' => [
        'oauth' => [
            'class' => 'backendApiOauth\Module',
        ],
        'v1' => [
            'class' => 'backendApiV1\Module',
        ],
        'oauth2' => [
            'class' => 'common\oauth2\server\Module',
            'accessTokenCryptKey' => 'test', // 字符串签名加密。
            /*'accessTokenCryptKey' => [ // 私钥文件加密。
                'privateKey' => '@common/oauth2/server/private.key',
                'passphrase' => '',
                'publicKey' => '@common/oauth2/server/public.key',
            ],*/
            'refreshTokenCryptKey' => [
                //'ascii' => 'def000008f058ff223434851b1d087e64bc0ac984be363bf9bb719c9cc8962fe53b2b0e61728e0d121df64493bd9a3089d5be0785fb3383d561aa44312bb97f211987368',
                //'path' => '@common/oauth2/server/refresh-token.txt',
                'password' => 'test',
            ],
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