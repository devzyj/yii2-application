<?php
/**
 * @link https://github.com/devzyj/yii2-admin
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
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
            'keyPrefix' => 'yii2admin',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;port=3306;dbname=yii2admin',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => 'yii2admin_',
            'charset' => 'utf8',
        ],
    ],
];
