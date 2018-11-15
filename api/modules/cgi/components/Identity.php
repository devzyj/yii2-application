<?php
/**
 * @link https://github.com/devzyj/yii2-app-rest
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgi\components;

/**
 * 访问 `cgi-bin` 接口的客户端标识类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Identity extends \api\components\Identity
{
    /******************************* IdentityInterface *******************************/
    /**
     * {@inheritdoc}
     * 
     * @param string $token 客户端标识符。
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOrSetOneByIdentifier($token);
    }
}
