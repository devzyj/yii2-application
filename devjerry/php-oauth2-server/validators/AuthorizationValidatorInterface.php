<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\validators;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\exceptions\InvalidAccessTokenException;

/**
 * 授权信息验证器接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AuthorizationValidatorInterface
{
    /**
     * 验证服务器请求。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws InvalidAccessTokenException 访问令牌无效。
     */
    public function validateServerRequest($request);
    
    /**
     * 验证访问令牌。
     * 
     * @param string $accessToken 访问令牌。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws InvalidAccessTokenException 访问令牌无效。
     */
    public function validateAccessToken($accessToken);
}