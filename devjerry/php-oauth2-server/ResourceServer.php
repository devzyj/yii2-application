<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\validators\AuthorizationValidatorInterface;
use devjerry\oauth2\server\exceptions\InvalidAccessTokenException;

/**
 * ResourceServer class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ResourceServer
{
    /**
     * @var AuthorizationValidatorInterface
     */
    private $_validator;
    
    /**
     * 获取授权验证器。
     *
     * @return AuthorizationValidatorInterface
     */
    public function getValidator()
    {
        return $this->_validator;
    }

    /**
     * 设置授权验证器。
     *
     * @param AuthorizationValidatorInterface $validator
     */
    public function setValidator($validator)
    {
        $this->_validator = $validator;
    }
    
    /**
     * 验证服务器请求中的授权信息。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws InvalidAccessTokenException 访问令牌无效。
     */
    public function validateServerRequest($request)
    {
        return $this->getValidator()->validateServerRequest($request);
    }

    /**
     * 验证访问令牌。
     *
     * @param string $accessToken 访问令牌。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws InvalidAccessTokenException 访问令牌无效。
     */
    public function validateAccessToken($accessToken)
    {
        return $this->getValidator()->validateAccessToken($accessToken);
    }
}