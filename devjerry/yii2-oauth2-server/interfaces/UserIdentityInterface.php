<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\interfaces;

use devzyj\oauth2\server\interfaces\UserEntityInterface;

/**
 * UserIdentityInterface 需要用户身份证验实例实现的接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface UserIdentityInterface
{
    /**
     * 获取授权用户实体对像。
     * 
     * @return UserEntityInterface
     */
    public function getOAuthUserEntity();
    
    /**
     * 获取用户是否同意授权。
     * 
     * @return boolean|null 返回 `null` 表示未进行同意或拒绝授权的操作。
     */
    public function getOAuthIsApproved();
}