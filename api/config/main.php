<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

$config = [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => [
        'log',
        'authorize',
        'cgi-bin/v1',
        'rbac/v1',
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
        'authorize' => [
            'class' => 'apiAuthorize\Module',
        ],
        'cgi-bin' => [
            'class' => 'apiCgiBin\Module',
            'modules' => [
                'v1' => [
                    'class' => 'apiCgiBinV1\Module',
                ],
            ],
        ],
        'rbac' => [
            'class' => 'apiRbac\Module',
            'modules' => [
                'v1' => [
                    'class' => 'apiRbacV1\Module',
                ],
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