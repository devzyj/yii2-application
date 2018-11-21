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
        'controller' => [
            "{$this->uniqueId}/client",
        ],
    ],
    // user rest api.
    [
        'class' => 'devzyj\rest\UrlRule',
        'controller' => [
            "{$this->uniqueId}/user",
        ],
        'extraTokens' => [
            '{relationId}' => '<relationid:\\d[\\d,]*>',
            '{relationIds}' => '<relationids:\\d[\\d,]*;[\\d,;]*>',
            '{code}' => '<code:\\w[\\w]*>',
            '{codes}' => '<codes:\\w[\\w]*;[\\w;]*>',
        ],
        'extraPatterns' => [
            'POST {id}/roles' => 'assign-roles',
            'POST {id}/roles/{relationId}' => 'assign-role',
            'DELETE {id}/roles' => 'remove-roles',
            'DELETE {id}/roles/{relationId}' => 'remove-role',
            'DELETE {id}/roles/{relationIds}' => 'remove-roles',
            'GET {id}/check-operation/{code}' => 'check-operation',
            'GET {id}/check-operations/{codes}' => 'check-operations',
        ],
    ],
    // role rest api.
    [
        'class' => 'devzyj\rest\UrlRule',
        'controller' => [
            "{$this->uniqueId}/role",
        ],
        'extraTokens' => [
            '{relationId}' => '<relationid:\\d[\\d,]*>',
            '{relationIds}' => '<relationids:\\d[\\d,]*;[\\d,;]*>',
        ],
        'extraPatterns' => [
            'POST {id}/permissions' => 'assign-permissions',
            'POST {id}/permissions/{relationId}' => 'assign-permission',
            'DELETE {id}/permissions' => 'remove-permissions',
            'DELETE {id}/permissions/{relationId}' => 'remove-permission',
            'DELETE {id}/permissions/{relationIds}' => 'remove-permissions',
            'POST {id}/users' => 'assign-users',
            'POST {id}/users/{relationId}' => 'assign-user',
            'DELETE {id}/users' => 'remove-users',
            'DELETE {id}/users/{relationId}' => 'remove-user',
            'DELETE {id}/users/{relationIds}' => 'remove-users',
        ],
    ],
    // permission rest api.
    [
        'class' => 'devzyj\rest\UrlRule',
        'controller' => [
            "{$this->uniqueId}/permission",
        ],
        'extraTokens' => [
            '{relationId}' => '<relationid:\\d[\\d,]*>',
            '{relationIds}' => '<relationids:\\d[\\d,]*;[\\d,;]*>',
        ],
        'extraPatterns' => [
            'POST {id}/operations' => 'assign-operations',
            'POST {id}/operations/{relationId}' => 'assign-operation',
            'DELETE {id}/operations' => 'remove-operations',
            'DELETE {id}/operations/{relationId}' => 'remove-operation',
            'DELETE {id}/operations/{relationIds}' => 'remove-operations',
            'POST {id}/roles' => 'assign-roles',
            'POST {id}/roles/{relationId}' => 'assign-role',
            'DELETE {id}/roles' => 'remove-roles',
            'DELETE {id}/roles/{relationId}' => 'remove-role',
            'DELETE {id}/roles/{relationIds}' => 'remove-roles',
        ],
    ],
    // operation rest api.
    [
        'class' => 'devzyj\rest\UrlRule',
        'controller' => [
            "{$this->uniqueId}/operation",
        ],
        'extraTokens' => [
            '{relationId}' => '<relationid:\\d[\\d,]*>',
            '{relationIds}' => '<relationids:\\d[\\d,]*;[\\d,;]*>',
        ],
        'extraPatterns' => [
            'POST {id}/permissions' => 'assign-permissions',
            'POST {id}/permissions/{relationId}' => 'assign-permission',
            'DELETE {id}/permissions' => 'remove-permissions',
            'DELETE {id}/permissions/{relationId}' => 'remove-permission',
            'DELETE {id}/permissions/{relationIds}' => 'remove-permissions',
        ],
    ],
];