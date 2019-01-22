<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 用户实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface UserEntityInterface
{
    /**
     * 获取用户的标识符。
     *
     * @return string
     */
    public function getIdentifier();
    
    /**
     * 获取用户的全部权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopeEntities();
    
    /**
     * 获取用户的默认权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getDefaultScopeEntities();
}