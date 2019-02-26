<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

/**
 * dev configuration.
 */
$config = [
    'language' => 'zh-CN',
    'bootstrap'=> ['debug', 'gii'],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/app-dev.log',
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'fssPsrBrJhag6a2kK-Pwu6QuxDtKo-mK',
        ],
    ],
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '10.111.222.1'],
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '10.111.222.1'],
            'generators' => [
                'crud' => [
                    'class' => 'yii\gii\generators\crud\Generator',
                    'templates' => [
                        'adminlte' => '@vendor/dmstr/yii2-adminlte-asset/gii/templates/crud/simple',
                    ]
                ],
                'model' => [
                    'class' => 'yii\gii\generators\model\Generator',
                    'templates' => [
                        'backend-1.0' => '@backend/gii/templates/model/default-1.0',
                    ]
                ],
            ],
        ]
    ],
];

return $config;
