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
            ],
        ],
        'oauthClient' => [
            'class' => 'yii\httpclient\Client',
            'baseUrl' => 'http://auth.backend.application.yii2.devzyj.zyj/oauth2',
        ],
    ],
];

return $config;
