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
];