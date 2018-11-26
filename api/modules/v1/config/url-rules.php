<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

return [
    // client rest api.
    [
        'class' => 'devzyj\rest\UrlRule',
        'extraTokens' => [
            '{id}' => '<id:[0-9a-zA-Z][0-9a-zA-Z]*>',
            '{ids}' => '<ids:[0-9a-zA-Z][0-9a-zA-Z]*;[0-9a-zA-Z;]*>',
        ],
        'controller' => [
            "{$this->uniqueId}/client",
        ],
        'extraPatterns' => [
            'PUT {id}/reset-id' => 'reset-id',
            'PUT {id}/reset-secret' => 'reset-secret',
            'DELETE {id}/cache' => 'delete-cache',
        ],
    ],
];