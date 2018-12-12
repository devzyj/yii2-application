<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server;

use devjerry\oauth2\server\authorizes\AuthorizeTypeInterface;
use devjerry\oauth2\server\authorizes\AuthorizeRequestInterface;
use devjerry\oauth2\server\grants\GrantTypeInterface;
use devjerry\oauth2\server\exceptions\OAuthServerException;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;

/**
 * AuthorizationServer class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationServer
{
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
    public function getAuthorizeTypes()
    {
        return $this->_authorizeTypes;
    }
    
    /**
     * 获取权限授予类型。
     * 
     * @return GrantTypeInterface[] 权限授予类型实例列表。
     */
    public function getGrantTypes()
    {
        return $this->_grantTypes;
    }

    /**
     * 添加授权类型。
     *
     * @param AuthorizeTypeInterface $authorizeType 授权类型。
     */
    public function addAuthorizeType(AuthorizeTypeInterface $authorizeType)
    {
        $this->_grantTypes[$authorizeType->getIdentifier()] = $authorizeType;
    }
    
    /**
     * 添加权限授予类型。
     * 
     * @param GrantTypeInterface $grantType 权限授予类型。
     */
    public function addGrantType(GrantTypeInterface $grantType)
    {
        $this->_grantTypes[$grantType->getIdentifier()] = $grantType;
    }
    
    /**
     * 获取授权请求。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return AuthorizeRequestInterface 授权请求。
     */
    public function getAuthorizeRequest(ServerRequestInterface $request)
    {
        foreach ($this->getAuthorizeTypes() as $identifier => $authorizeType) {
            if ($authorizeType->canRun($request)) {
                return $authorizeType->getAuthorizeRequest($request);
            }
        }
        
        throw new OAuthServerException(400, 'The authorization type is not supported by the authorization server.');
    }
    
    /**
     * 运行授权。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest 授权请求。
     * @return array 回调地址信息。
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
     */
    public function runGrantTypes(ServerRequestInterface $request)
    {
        foreach ($this->getGrantTypes() as $identifier => $grantType) {
            if ($grantType->canRun($request)) {
                return $grantType->run($request);
            }
        }
        
        throw new OAuthServerException(400, 'The authorization grant type is not supported by the authorization server.');
    }
}