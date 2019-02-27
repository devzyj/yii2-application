<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

/**
 * prod configuration.
 */
return [
    'timeZone' => 'Asia/Shanghai',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'keyPrefix' => 'yii2application',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=yii2application',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => '',
            'charset' => 'utf8',
        ],
        'db_backend' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=yii2application',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => 'backend_',
            'charset' => 'utf8',
        ],
    ],
];
