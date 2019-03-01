<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

$config = [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    //'sourceLanguage' => 'zh-CN',
    'controllerNamespace' => 'backend\controllers',
    'aliases' => [
        //'@library' => '@backend/library',
    ],
    'bootstrap' => ['log'],
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
            'csrfParam' => '_csrf-app-backend',
            'csrfCookie' => ['httpOnly' => true],
            //'csrfCookie' => ['httpOnly' => true, 'path' => '/admin'], // Shared Hosting Environment
        ],
        'session' => [
            'name' => 'app-backend',
            'cookieParams' => ['httponly' => true],
            //'cookieParams' => ['httponly' => true, 'path' => '/admin'], // Shared Hosting Environment
        ],
        'user' => [
            'identityClass' => 'backend\components\Identity',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-app-backend', 'httpOnly' => true],
            //'identityCookie' => ['name' => '_identity-app-backend', 'httpOnly' => true, 'path' => '/admin'], // Shared Hosting Environment
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '',
            'rules' => [
                'register' => 'site/register',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'profile' => 'site/profile',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@backend/views' => [
                        '@backend/views', 
                        '@devzyj/yii2/adminlte/views',
                    ],
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'devzyj\yii2\adminlte\web\AdminLteAsset' => [
                    //'layout' => 'fixed',
                ],
            ],
        ],
        /*'i18n' => [
            'translations' => [
                'adminlte' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                ],
            ],
        ],*/
    ],
    'modules' => [
        
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