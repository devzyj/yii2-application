<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\demos;

use devzyj\oauth2\server\interfaces\UserEntityInterface;
use devjerry\yii2\oauth2\server\interfaces\OAuthUserEntityInterface;

/**
 * DemoUserEntity class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class DemoUserEntity extends DemoUserModel implements UserEntityInterface, OAuthUserEntityInterface
{
    /******************************** UserEntityInterface ********************************/
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultScopeEntities()
    {
        return $this->getDefaultScopes();
    }

    /******************************** OAuthUserEntityInterface ********************************/
    /**
     * {@inheritdoc}
     */
    public function getScopeEntities()
    {
        return $this->getScopes();
    }
}