<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\interfaces;

use yii\web\User;

/**
 * OAuthAuthorizationFormInterface class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface OAuthAuthorizationFormInterface
{
    /**
     * 用户授权。
     * 
     * @param User $user
     * @return boolean
     */
    public function authorization(User $user);
}