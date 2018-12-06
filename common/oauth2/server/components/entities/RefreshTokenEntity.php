<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\entities;

use common\oauth2\server\interfaces\RefreshTokenEntityInterface;
use common\oauth2\server\components\entities\traits\RefreshTokenEntityTrait;

/**
 * RefreshTokenEntity class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RefreshTokenEntity implements RefreshTokenEntityInterface
{
    use RefreshTokenEntityTrait;
}