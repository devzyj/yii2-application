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
    'modules' => [
        'oauth2' => [
            'user' => [
                'class' => 'yii\web\User',
                'identityClass' => '',
            ],
            'loginUrl' => [],
            'authorizationUrl' => [],
        ],
    ],
];
