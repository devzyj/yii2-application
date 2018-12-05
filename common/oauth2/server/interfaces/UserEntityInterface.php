<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 用户实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface UserEntityInterface
{
    /**
     * 获取用户的标识符。
     *
     * @return string 用户的标识符。
     */
    public function getIdentifier();
}