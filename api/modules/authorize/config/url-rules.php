<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

return [
    // 客户端授权，获取 ACCESS_TOKEN。
    "POST <module:({$this->uniqueId})>/clients" => "<module>/default/client-credentials",
];