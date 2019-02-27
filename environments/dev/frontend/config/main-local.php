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
        'request' => [
            'cookieValidationKey' => '',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/app-dev.log',
                ],
            ],
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
        ]
    ],
];

return $config;
