<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

return [
    // generate access token.
    "<module:({$this->uniqueId})>/token" => "<module>/token/create-json-web-token",
];