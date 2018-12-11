<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\entities;

use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\yii2\oauth2\server\entities\traits\AccessTokenEntityTrait;

/**
 * AccessTokenEntity class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AccessTokenEntity implements AccessTokenEntityInterface
{
    use AccessTokenEntityTrait;
}