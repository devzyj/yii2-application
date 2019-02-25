<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models\oauth2;

use devzyj\oauth2\server\interfaces\UserRepositoryInterface;

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
        /* @var $model UserEntity */
        $model = UserEntity::findOne($identifier);
        if ($model && $model->getIsValid()) {
            return $model;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByCredentials($username, $password)
    {
        /* @var $model UserEntity */
        $model = UserEntity::findOneByUsername($username);
        if ($model && $model->getIsValid() && $model->validatePassword($password)) {
            return $model;
        }
    }
}