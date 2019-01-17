<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities;

use common\oauth2\server\interfaces\UserEntityInterface;

/**
 * UserEntity class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UserEntity implements UserEntityInterface
{
    public $id;
    
    public $username;
    
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
    public function getScopeEntities()
    {
        return ScopeEntity::findAll([1 ,2, 3]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDefaultScopeEntities()
    {
        return ScopeEntity::findAll([1, 3]);
    }
}