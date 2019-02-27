<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

return [
    // client rest api.
    [
        'class' => 'devzyj\rest\UrlRule',
        'controller' => [
            "{$this->uniqueId}/client",
        ],
        'extraPatterns' => [
            'PUT {id}/reset-identifier' => 'reset-identifier',
            'PUT {id}/reset-secret' => 'reset-secret',
        ],
        /*'extraTokens' => [
            '{identifier}' => '<id:[0-9a-zA-Z][0-9a-zA-Z]*>',
            '{identifiers}' => '<ids:[0-9a-zA-Z][0-9a-zA-Z]*;[0-9a-zA-Z;]*>',
        ],*/
    ],
];