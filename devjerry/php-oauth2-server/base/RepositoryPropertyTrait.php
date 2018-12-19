<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

use devjerry\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use devjerry\oauth2\server\interfaces\AuthorizationCodeRepositoryInterface;
use devjerry\oauth2\server\interfaces\ClientRepositoryInterface;
use devjerry\oauth2\server\interfaces\RefreshTokenRepositoryInterface;
use devjerry\oauth2\server\interfaces\ScopeRepositoryInterface;
use devjerry\oauth2\server\interfaces\UserRepositoryInterface;

/**
 * RepositoryPropertyTrait
 *
 * @property AccessTokenRepositoryInterface $accessTokenRepository 访问令牌存储库。
 * @property AuthorizationCodeRepositoryInterface $authorizationCodeRepository 授权码存储库。
 * @property ClientRepositoryInterface $clientRepository 客户端存储库。
 * @property RefreshTokenRepositoryInterface $refreshTokenRepository 更新令牌存储库。
 * @property ScopeRepositoryInterface $scopeRepository 权限存储库。
 * @property UserRepositoryInterface $userRepository 用户存储库。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait RepositoryPropertyTrait
{
    /**
     * @var AccessTokenRepositoryInterface 访问令牌存储库。
     */
    private $_accessTokenRepository;

    /**
     * @var AuthorizationCodeRepositoryInterface 授权码存储库。
     */
    private $_authorizationCodeRepository;
    
    /**
     * @var ClientRepositoryInterface 客户端存储库。
     */
    private $_clientRepository;

    /**
     * @var RefreshTokenRepositoryInterface 更新令牌存储库。
     */
    private $_refreshTokenRepository;
    
    /**
     * @var ScopeRepositoryInterface 权限存储库。
     */
    private $_scopeRepository;

    /**
     * @var UserRepositoryInterface 用户存储库。
     */
    private $_userRepository;
    
    /**
     * 获取访问令牌存储库。
     *
     * @return AccessTokenRepositoryInterface
     */
    public function getAccessTokenRepository()
    {
        return $this->_accessTokenRepository;
    }
    
    /**
     * 设置访问令牌存储库。
     *
     * @param AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository)
    {
        $this->_accessTokenRepository = $accessTokenRepository;
    }

    /**
     * 获取授权码存储库。
     *
     * @return AuthorizationCodeRepositoryInterface
     */
    public function getAuthorizationCodeRepository()
    {
        return $this->_authorizationCodeRepository;
    }
    
    /**
     * 设置授权码存储库。
     *
     * @param AuthorizationCodeRepositoryInterface $authorizationCodeRepository
     */
    public function setAuthorizationCodeRepository(AuthorizationCodeRepositoryInterface $authorizationCodeRepository)
    {
        $this->_authorizationCodeRepository = $authorizationCodeRepository;
    }
    
    /**
     * 获取客户端存储库。
     *
     * @return ClientRepositoryInterface
     */
    public function getClientRepository()
    {
        return $this->_clientRepository;
    }
    
    /**
     * 设置客户端存储库。
     *
     * @param ClientRepositoryInterface $clientRepository
     */
    public function setClientRepository(ClientRepositoryInterface $clientRepository)
    {
        $this->_clientRepository = $clientRepository;
    }

    /**
     * 获取更新令牌存储库。
     *
     * @return RefreshTokenRepositoryInterface
     */
    public function getRefreshTokenRepository()
    {
        return $this->_refreshTokenRepository;
    }
    
    /**
     * 设置更新令牌存储库。
     *
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function setRefreshTokenRepository(RefreshTokenRepositoryInterface $refreshTokenRepository)
    {
        $this->_refreshTokenRepository = $refreshTokenRepository;
    }
    
    /**
     * 获取权限存储库。
     *
     * @return ScopeRepositoryInterface
     */
    public function getScopeRepository()
    {
        return $this->_scopeRepository;
    }
    
    /**
     * 设置权限存储库。
     *
     * @param ScopeRepositoryInterface $scopeRepository
     */
    public function setScopeRepository(ScopeRepositoryInterface $scopeRepository)
    {
        $this->_scopeRepository = $scopeRepository;
    }

    /**
     * 获取用户存储库。
     *
     * @return UserRepositoryInterface
     */
    public function getUserRepository()
    {
        return $this->_userRepository;
    }
    
    /**
     * 设置用户存储库。
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function setUserRepository(UserRepositoryInterface $userRepository)
    {
        $this->_userRepository = $userRepository;
    }
}