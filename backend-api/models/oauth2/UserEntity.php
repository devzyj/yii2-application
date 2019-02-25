<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models\oauth2;

use devzyj\oauth2\server\interfaces\UserEntityInterface;
use backendApi\models\Admin;

/**
 * UserEntity class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UserEntity extends Admin implements UserEntityInterface
{
    /******************************** UserEntityInterface ********************************/
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->id;
    }
}