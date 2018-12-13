<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\exceptions\OAuthServerException;

/**
 * PasswordGrant class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class PasswordGrant extends AbstractGrant
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::GRANT_TYPE_PASSWORD;
    }

    /**
     * {@inheritdoc}
     */
    protected function runGrant(ServerRequestInterface $request, ClientEntityInterface $client)
    {
        // 获取正在请求授权的用户。
        $user = $this->getAuthorizeUser($request);
        
        // 获取请求的权限。
        $requestedScopes = $this->getRequestedScopes($request, $this->ensureDefaultScopes($user));
        
        // 确定最终授予的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($requestedScopes, $this->getIdentifier(), $client, $user);
        
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client, $user);

        // 创建更新令牌。
        $refreshToken = $this->generateRefreshToken($accessToken);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($accessToken, $refreshToken);
    }
    
    /**
     * 获取正在请求授权的用户。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return UserEntityInterface 用户实例。
     * @throws OAuthServerException 缺少参数。
     */
    protected function getAuthorizeUser(ServerRequestInterface $request)
    {
        // 获取用户的认证信息。
        $username = $this->getRequestBodyParam($request, 'username');
        $password = $this->getRequestBodyParam($request, 'password');
        if ($username === null || $password === null) {
            throw new OAuthServerException(400, 'Missing parameters: "username" and "password" required.');
        }
        
        // 获取并返回用户实例。
        return $this->getUserByCredentials($username, $password);
    }

    /**
     * 使用用户认证信息，获取用户实例。
     *
     * @param string $username 用户名。
     * @param string $password 用户密码。
     * @return UserEntityInterface 用户实例。
     */
    protected function getUserByCredentials($username, $password)
    {
        $user = $this->getUserRepository()->getUserEntityByCredentials($username, $password);
        if (!$user instanceof UserEntityInterface) {
            throw new OAuthServerException(401, 'User authentication failed.');
        }
        
        return $user;
    }
}