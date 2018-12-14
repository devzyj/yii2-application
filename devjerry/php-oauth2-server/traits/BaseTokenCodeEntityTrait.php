<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\traits;

use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;

/**
 * BaseTokenCodeEntityTrait  实现了 [[BaseTokenCodeEntityInterface]] 中的方法。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait BaseTokenCodeEntityTrait
{
    /**
     * @var string
     */
    private $_identifier;
    
    /**
     * @var integer
     */
    private $_expires;

    /**
     * @var string
     */
    private $_clientIdentifier;
    
    /**
     * @var ClientEntityInterface
     */
    private $_clientEntity;

    /**
     * @var string
     */
    private $_userIdentifier;
    
    /**
     * @var UserEntityInterface
     */
    private $_userEntity;

    /**
     * @var array
     */
    private $_scopeIdentifiers = [];
    
    /**
     * @var ScopeEntityInterface[]
     */
    private $_scopeEntities = [];

    /**
     * 获取标识符。
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }
    
    /**
     * 设置标识符。
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = $identifier;
    }

    /**
     * 获取过期时间。
     *
     * @return integer 时间戳。
     */
    public function getExpires()
    {
        return $this->_expires;
    }
    
    /**
     * 设置过期时间。
     * 
     * @param integer $expires 时间戳。
     */
    public function setExpires($expires)
    {
        $this->_expires = $expires;
    }

    /**
     * 获取关联的客户端标识。
     *
     * @return string
     */
    public function getClientIdentifier()
    {
        return $this->_clientIdentifier;
    }
    
    /**
     * 设置关联的客户端标识。
     *
     * @param string $clientIdentifier
     */
    public function setClientIdentifier($clientIdentifier)
    {
        $this->_clientIdentifier = $clientIdentifier;
    }
    
    /**
     * 获取关联的客户端。
     *
     * @return ClientEntityInterface
     */
    public function getClientEntity()
    {
        return $this->_clientEntity;
    }
    
    /**
     * 设置关联的客户端。
     * 
     * @param ClientEntityInterface $clientEntity
     */
    public function setClientEntity(ClientEntityInterface $clientEntity)
    {
        $this->_clientEntity = $clientEntity;
    }

    /**
     * 获取关联的用户标识。
     *
     * @return string
     */
    public function getUserIdentifier()
    {
        return $this->_userIdentifier;
    }
    
    /**
     * 设置关联的用户标识。
     *
     * @param string $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->_userIdentifier = $userIdentifier;
    }
    
    /**
     * 获取关联的用户。
     *
     * @return UserEntityInterface
     */
    public function getUserEntity()
    {
        return $this->_userEntity;
    }

    /**
     * 设置关联的用户。
     *
     * @param UserEntityInterface $userEntity
     */
    public function setUserEntity(UserEntityInterface $userEntity)
    {
        $this->_userEntity = $userEntity;
    }
    
    /**
     * 获取关联的权限标识符。
     *
     * @return string[]
     */
    public function getScopeIdentifiers()
    {
        return array_keys($this->_scopeIdentifiers);
    }
    
    /**
     * 添加关联的权限标识符。
     *
     * @param string $scopeIdentifier
     */
    public function addScopeIdentifier($scopeIdentifier)
    {
        $this->_scopeIdentifiers[$scopeIdentifier] = true;
    }
    
    /**
     * 获取关联的权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopeEntities()
    {
        return array_values($this->_scopeEntities);
    }

    /**
     * 添加关联的权限。
     * 
     * @param ScopeEntityInterface $scopeEntity
     */
    public function addScopeEntity(ScopeEntityInterface $scopeEntity)
    {
        $this->_scopeEntities[$scopeEntity->getIdentifier()] = $scopeEntity;
    }

}