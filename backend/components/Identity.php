<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backend\components;

use yii\web\IdentityInterface;

/**
 * 访问后台的用户标识类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Identity extends \yii\base\Model implements IdentityInterface
{
    /******************************* IdentityInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }
}
