<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server;

use devjerry\oauth2\server\exceptions\OAuthServerException;
use devjerry\oauth2\server\interfaces\GrantTypeInterface;
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
     * @var GrantTypeInterface[]
     */
    private $_grantTypes = [];
    
    /**
     * 获取授权类型。
     * 
     * @return GrantTypeInterface[] 授权类型实例列表。
     */
    public function getGrantTypes()
    {
        return $this->_grantTypes;
    }
    
    /**
     * 添加授权类型。
     * 
     * @param GrantTypeInterface $grantType 授权类型。
     */
    public function addGrantType(GrantTypeInterface $grantType)
    {
        $this->_grantTypes[$grantType->getIdentifier()] = $grantType;
    }
    
    /**
     * 运行授权。
     * 
     * @param ServerRequestInterface $request 服务器请求。
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