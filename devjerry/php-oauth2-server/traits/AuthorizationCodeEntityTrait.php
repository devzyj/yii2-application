<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\traits;

use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;

/**
 * AuthorizationCodeEntityTrait 实现了 [[AuthorizationCodeEntityInterface]] 中的方法。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait AuthorizationCodeEntityTrait
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
    private $_redirectUri;
    
    /**
     * @var ClientEntityInterface
     */
    private $_clientEntity;
    
    /**
     * @var UserEntityInterface
     */
    private $_userEntity;
    
    /**
     * @var ScopeEntityInterface[]
     */
    private $_scopeEntities = [];

    /**
     * @var string
     */
    private $_codeChallenge;

    /**
     * @var string
     */
    private $_codeChallengeMethod;
    
    /**
     * 获取授权码的标识符。
     *
     * @return string 授权码的标识符。
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }
    
    /**
     * 设置授权码的标识符。
     *
     * @param string $identifier 授权码的标识符。
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = $identifier;
    }

    /**
     * 获取授权码的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires()
    {
        return $this->_expires;
    }
    
    /**
     * 设置授权码的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires)
    {
        $this->_expires = $expires;
    }

    /**
     * 获取授权码的回调地址。
     *
     * @return string|null 回调地址。
     */
    public function getRedirectUri()
    {
        return $this->_redirectUri;
    }
    
    /**
     * 设置授权码的回调地址。
     *
     * @param string $uri 回调地址。
     */
    public function setRedirectUri($redirectUri)
    {
        $this->_redirectUri = $redirectUri;
    }
    
    /**
     * 获取与授权码关联的客户端。
     *
     * @return ClientEntityInterface 客户端实例。
     */
    public function getClientEntity()
    {
        return $this->_clientEntity;
    }
    
    /**
     * 设置与授权码关联的客户端。
     * 
     * @param ClientEntityInterface $clientEntity 客户端实例。
     */
    public function setClientEntity(ClientEntityInterface $clientEntity)
    {
        $this->_clientEntity = $clientEntity;
    }

    /**
     * 获取与授权码关联的用户。
     *
     * @return UserEntityInterface 用户实例。
     */
    public function getUserEntity()
    {
        return $this->_userEntity;
    }
    
    /**
     * 设置与授权码关联的用户。
     *
     * @param UserEntityInterface $userEntity 用户实例。
     */
    public function setUserEntity(UserEntityInterface $userEntity)
    {
        $this->_userEntity = $userEntity;
    }

    /**
     * 获取与授权码关联的权限。
     *
     * @return ScopeEntityInterface[] 权限实例列表。
     */
    public function getScopeEntities()
    {
        return array_values($this->_scopeEntities);
    }

    /**
     * 添加与授权码关联的权限。
     * 
     * @param ScopeEntityInterface $scopeEntity 权限实例。
     */
    public function addScopeEntity(ScopeEntityInterface $scopeEntity)
    {
        $this->_scopeEntities[$scopeEntity->getIdentifier()] = $scopeEntity;
    }

    /**
     * 获取交换验证代码。
     *
     * @return string 交换验证代码。
     */
    public function getCodeChallenge()
    {
        return $this->_codeChallenge;
    }
    
    /**
     * 设置交换验证代码。
     *
     * @param string $codeChallenge 交换验证代码。
    */
    public function setCodeChallenge($codeChallenge)
    {
        $this->_codeChallenge = $codeChallenge;
    }

    /**
     * 获取交换验证方法。
     *
     * @return string 交换验证方法。
     */
    public function getCodeChallengeMethod()
    {
        return $this->_codeChallengeMethod;
    }
    
    /**
     * 设置交换验证方法。
     *
     * @param string $codeChallengeMethod 交换验证方法。
    */
    public function setCodeChallengeMethod($codeChallengeMethod)
    {
        $this->_codeChallengeMethod = $codeChallengeMethod;
    }
}