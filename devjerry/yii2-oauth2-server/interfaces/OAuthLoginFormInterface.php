<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\interfaces;

use yii\web\User;

/**
 * OAuthLoginFormInterface class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface OAuthLoginFormInterface
{
    /**
     * 用户登录。
     * 
     * @param User $user
     * @return boolean
     */
    public function login(User $user);
}