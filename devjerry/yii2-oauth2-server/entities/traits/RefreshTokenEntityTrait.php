<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\entities\traits;

use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;

/**
 * RefreshTokenEntityTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait RefreshTokenEntityTrait
{
    use EntityTrait;

    /**
     * @var integer
     */
    private $_expires;
    
    /**
     * @var string
     */
    private $_clientIdentifier;

    /**
     * @var string
     */
    private $_userIdentifier;
    
    /**
     * @var array
     */
    private $_scopeIdentifiers = [];

    /**
     * @var string
     */
    private $_accessTokenIdentifier;
    
    /**
     * @var AccessTokenEntityInterface
     */
    private $_accessTokenEntity;

    /**
     * 获取更新令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires()
    {
        return $this->_expires;
    }

    /**
     * 设置更新令牌的过期时间。
     *
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires)
    {
        $this->_expires = $expires;
    }

    /**
     * 获取与更新令牌关联的客户端标识。
     *
     * @return string 客户端标识。
     */
    public function getClientIdentifier()
    {
        return $this->_clientIdentifier;
    }

    /**
     * 设置与更新令牌关联的客户端标识。
     *
     * @param string $clientIdentifier 客户端标识。
     */
    public function setClientIdentifier($clientIdentifier)
    {
        $this->_clientIdentifier = $clientIdentifier;
    }
    
    /**
     * 获取与更新令牌关联的用户标识。
     *
     * @return string 用户标识。
     */
    public function getUserIdentifier()
    {
        return $this->_userIdentifier;
    }
    
    /**
     * 设置与更新令牌关联的用户标识。
     *
     * @param string $userIdentifier 用户标识。
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->_userIdentifier = $userIdentifier;
    }
    
    /**
     * 获取与更新令牌关联的权限标识符。
     *
     * @return string[] 权限标识符列表。
     */
    public function getScopeIdentifiers()
    {
        return array_keys($this->_scopeIdentifiers);
    }
    
    /**
     * 添加与更新令牌关联的权限标识符。
     *
     * @param string $scopeIdentifier 权限标识符。
     */
    public function addScopeIdentifier($scopeIdentifier)
    {
        $this->_scopeIdentifiers[$scopeIdentifier] = true;
    }

    /**
     * 获取与更新令牌关联的访问令牌标识。
     *
     * @param string 访问令牌标识。
     */
    public function getAccessTokenIdentifier()
    {
        return $this->_accessTokenIdentifier;
    }
    
    /**
     * 设置与更新令牌关联的访问令牌标识。
     * 
     * @param string $accessTokenIdentifier 访问令牌标识。
     */
    public function setAccessTokenIdentifier($accessTokenIdentifier)
    {
        $this->_accessTokenIdentifier = $accessTokenIdentifier;
    }
    
    /**
     * 获取与更新令牌关联的访问令牌。
     *
     * @return AccessTokenEntityInterface 访问令牌实例。
     */
    public function getAccessTokenEntity()
    {
        return $this->_accessTokenEntity;
    }
    
    /**
     * 设置与更新令牌关联的访问令牌。
     * 
     * @param AccessTokenEntityInterface $accessTokenEntity 访问令牌实例。
     */
    public function setAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->_accessTokenEntity = $accessTokenEntity;
    }
}