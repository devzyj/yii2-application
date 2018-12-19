<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server;

use devjerry\oauth2\server\base\AuthorizeGrantPropertyTrait;
use devjerry\oauth2\server\base\RepositoryPropertyTrait;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\authorizes\AuthorizeTypeInterface;
use devjerry\oauth2\server\authorizes\CodeAuthorize;
use devjerry\oauth2\server\authorizes\ImplicitAuthorize;
use devjerry\oauth2\server\authorizes\AuthorizeRequestInterface;
use devjerry\oauth2\server\grants\GrantTypeInterface;
use devjerry\oauth2\server\grants\AuthorizationCodeGrant;
use devjerry\oauth2\server\grants\ClientCredentialsGrant;
use devjerry\oauth2\server\grants\PasswordGrant;
use devjerry\oauth2\server\grants\RefreshTokenGrant;
use devjerry\oauth2\server\exceptions\UnsupportedAuthTypeException;
use devjerry\oauth2\server\exceptions\UserDeniedAuthorizeException;

/**
 * AuthorizationServer class.
 * 
 * ```php
 * 
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationServer
{
    use AuthorizeGrantPropertyTrait, RepositoryPropertyTrait;
    
    /**
     * @var AuthorizeTypeInterface[]
     */
    private $_authorizeTypes = [];
    
    /**
     * @var GrantTypeInterface[]
     */
    private $_grantTypes = [];

    /**
     * 获取授权类型。
     *
     * @return AuthorizeTypeInterface[] 授权类型实例列表。
     */
    protected function getAuthorizeTypes()
    {
        return $this->_authorizeTypes;
    }

    /**
     * 添加授权类型。
     *
     * @param AuthorizeTypeInterface $authorizeType 授权类型。
     * @param boolean $configure 是否使用全局配置。
     */
    public function addAuthorizeType(AuthorizeTypeInterface $authorizeType, $configure = true)
    {
        if ($configure) {
            if ($authorizeType instanceof CodeAuthorize) {
                $authorizeType->configure([
                    'authorizationCodeRepository' => $this->getAuthorizationCodeRepository(),
                    'clientRepository' => $this->getClientRepository(),
                    'scopeRepository' => $this->getScopeRepository(),
                    'defaultScopes' => $this->getDefaultScopes(),
                    'authorizationCodeDuration' => $this->getAuthorizationCodeDuration(),
                    'authorizationCodeCryptKey' => $this->getAuthorizationCodeCryptKey(),
                ]);
            } elseif ($authorizeType instanceof ImplicitAuthorize) {
                $authorizeType->configure([
                    'accessTokenRepository' => $this->getAccessTokenRepository(),
                    'clientRepository' => $this->getClientRepository(),
                    'scopeRepository' => $this->getScopeRepository(),
                    'defaultScopes' => $this->getDefaultScopes(),
                    'accessTokenDuration' => $this->getAccessTokenDuration(),
                    'accessTokenCryptKey' => $this->getAccessTokenCryptKey(),
                ]);
            }
        }
        
        $this->_grantTypes[$authorizeType->getIdentifier()] = $authorizeType;
    }
    
    /**
     * 获取权限授予类型。
     * 
     * @return GrantTypeInterface[] 权限授予类型实例列表。
     */
    protected function getGrantTypes()
    {
        return $this->_grantTypes;
    }
    
    /**
     * 添加权限授予类型。
     * 
     * @param GrantTypeInterface $grantType 权限授予类型。
     * @param boolean $configure 是否使用全局配置。
     */
    public function addGrantType(GrantTypeInterface $grantType, $configure = true)
    {
        if ($configure) {
            if ($grantType instanceof AuthorizationCodeGrant) {
                $grantType->configure([
                    'accessTokenRepository' => $this->getAccessTokenRepository(),
                    'authorizationCodeRepository' => $this->getAuthorizationCodeRepository(),
                    'clientRepository' => $this->getClientRepository(),
                    'refreshTokenRepository' => $this->getRefreshTokenRepository(),
                    'scopeRepository' => $this->getScopeRepository(),
                    'userRepository' => $this->getUserRepository(),
                    'accessTokenDuration' => $this->getAccessTokenDuration(),
                    'accessTokenCryptKey' => $this->getAccessTokenCryptKey(),
                    'authorizationCodeCryptKey' => $this->getAuthorizationCodeCryptKey(),
                    'refreshTokenDuration' => $this->getRefreshTokenDuration(),
                    'refreshTokenCryptKey' => $this->getRefreshTokenCryptKey(),
                ]);
            } elseif ($grantType instanceof ClientCredentialsGrant) {
                $grantType->configure([
                    'accessTokenRepository' => $this->getAccessTokenRepository(),
                    'clientRepository' => $this->getClientRepository(),
                    'scopeRepository' => $this->getScopeRepository(),
                    'defaultScopes' => $this->getDefaultScopes(),
                    'accessTokenDuration' => $this->getAccessTokenDuration(),
                    'accessTokenCryptKey' => $this->getAccessTokenCryptKey(),
                ]);
            } elseif ($grantType instanceof PasswordGrant) {
                $grantType->configure([
                    'accessTokenRepository' => $this->getAccessTokenRepository(),
                    'clientRepository' => $this->getClientRepository(),
                    'refreshTokenRepository' => $this->getRefreshTokenRepository(),
                    'scopeRepository' => $this->getScopeRepository(),
                    'userRepository' => $this->getUserRepository(),
                    'defaultScopes' => $this->getDefaultScopes(),
                    'accessTokenDuration' => $this->getAccessTokenDuration(),
                    'accessTokenCryptKey' => $this->getAccessTokenCryptKey(),
                    'refreshTokenDuration' => $this->getRefreshTokenDuration(),
                    'refreshTokenCryptKey' => $this->getRefreshTokenCryptKey(),
                ]);
            } elseif ($grantType instanceof RefreshTokenGrant) {
                $grantType->configure([
                    'accessTokenRepository' => $this->getAccessTokenRepository(),
                    'clientRepository' => $this->getClientRepository(),
                    'refreshTokenRepository' => $this->getRefreshTokenRepository(),
                    'scopeRepository' => $this->getScopeRepository(),
                    'userRepository' => $this->getUserRepository(),
                    'accessTokenDuration' => $this->getAccessTokenDuration(),
                    'accessTokenCryptKey' => $this->getAccessTokenCryptKey(),
                    'refreshTokenDuration' => $this->getRefreshTokenDuration(),
                    'refreshTokenCryptKey' => $this->getRefreshTokenCryptKey(),
                ]);
            }
        }
        
        $this->_grantTypes[$grantType->getIdentifier()] = $grantType;
    }
    
    /**
     * 获取授权请求。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return AuthorizeRequestInterface 授权请求。
     * @throws UnsupportedAuthTypeException 不支持的授权类型。
     */
    public function getAuthorizeRequest($request)
    {
        foreach ($this->getAuthorizeTypes() as $identifier => $authorizeType) {
            if ($authorizeType->canRun($request)) {
                return $authorizeType->getAuthorizeRequest($request);
            }
        }
        
        throw new UnsupportedAuthTypeException('The authorization type is not supported by the authorization server.');
    }
    
    /**
     * 运行授权。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest 授权请求。
     * @return string 回调地址。
     * @throws UserDeniedAuthorizeException 用户拒绝授权。
     */
    public function runAuthorizeTypes(AuthorizeRequestInterface $authorizeRequest)
    {
        return $authorizeRequest->getAuthorizeType()->run($authorizeRequest);
    }
    
    /**
     * 运行权限授予。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return array 认证信息。
     * @throws UnsupportedAuthTypeException 不支持的授予类型。
     */
    public function runGrantTypes($request)
    {
        foreach ($this->getGrantTypes() as $identifier => $grantType) {
            if ($grantType->canRun($request)) {
                return $grantType->run($request);
            }
        }
        
        throw new UnsupportedAuthTypeException('The grant type is not supported by the authorization server.');
    }
}