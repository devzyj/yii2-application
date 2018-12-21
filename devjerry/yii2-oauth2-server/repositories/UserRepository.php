<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\repositories;

use devzyj\oauth2\server\interfaces\UserRepositoryInterface;
use devjerry\yii2\oauth2\server\entities\UserEntity;

/**
 * UserRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntity($identifier)
    {
        if ($identifier == 1) {
            $user = new UserEntity();
            $user->id = 1;
            $user->username = 'jerry';
            return $user;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByCredentials($username, $password)
    {
        if ($username == 'jerry' && $password == '123456') {
            $user = new UserEntity();
            $user->id = 1;
            $user->username = $username;
            return $user;
        }
    }
}