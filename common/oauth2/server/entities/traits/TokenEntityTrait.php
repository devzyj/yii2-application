<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities\traits;

use common\oauth2\server\interfaces\ClientEntityInterface;
use common\oauth2\server\interfaces\ScopeEntityInterface;
use common\oauth2\server\interfaces\UserEntityInterface;

/**
 * TokenEntityTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait TokenEntityTrait
{
    use EntityTrait;

    /**
     * @var integer 令牌的过期时间。
     */
    private $_expires;
    
    /**
     * @var ClientEntityInterface 与令牌关联的客户端。
     */
    private $_client;
    
    /**
     * @var ScopeEntityInterface[] 与令牌关联的权限。
     */
    private $_scopes = [];
    
    /**
     * @var UserEntityInterface 与令牌关联的用户。
    */
    private $_user;

    /**
     * 获取令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires()
    {
        return $this->_expires;
    }
    
    /**
     * 设置令牌的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires)
    {
        $this->_expires = $expires;
    }
    
    /**
     * 获取与令牌关联的客户端。
     *
     * @return ClientEntityInterface
     */
    public function getClient()
    {
        return $this->_client;
    }
    
    /**
     * 设置与令牌关联的客户端。
     * 
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->_client = $client;
    }
    
    /**
     * 获取与令牌关联的权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes()
    {
        return array_values($this->_scopes);
    }

    /**
     * 添加与令牌关联的权限。
     * 
     * @param ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope)
    {
        $this->_scopes[$scope->getIdentifier()] = $scope;
    }

    /**
     * 获取与令牌关联的用户。
     *
     * @return UserEntityInterface
     */
    public function getUser()
    {
        return $this->_user;
    }
    
    /**
     * 设置与令牌关联的用户。
     *
     * @param UserEntityInterface $user
     */
    public function setUser(UserEntityInterface $user)
    {
        $this->_user = $user;
    }
}