<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\RefreshTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\exceptions\OAuthServerException;
use devjerry\oauth2\server\interfaces\UserEntityInterface;

/**
 * RefreshTokenGrant class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RefreshTokenGrant extends AbstractGrant
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::GRANT_TYPE_REFRESH_TOKEN;
    }

    /**
     * {@inheritdoc}
     */
    protected function runGrant($request, ClientEntityInterface $client)
    {
        // 获取请求的更新令牌。
        $requestedRefreshToken = $this->getRequestedRefreshToken($request);
        
        // 验证请求的更新令牌。
        $this->validateRefreshToken($requestedRefreshToken, $client);
        
        // 获取请求的权限。
        $refreshTokenScopeIdentifiers = $requestedRefreshToken->getScopeIdentifiers();
        $requestedScopes = $this->getRequestedScopes($request, $refreshTokenScopeIdentifiers);
        
        // 验证请求的权限是否超出更新令牌中的权限范围。
        foreach ($requestedScopes as $scope) {
            /* @var $scope ScopeEntityInterface */
            if (!in_array($scope->getIdentifier(), $refreshTokenScopeIdentifiers, true)) {
                throw new OAuthServerException(400, 'The requested scope is invalid.');
            }
        }
        
        // 获取与更新令牌关联的用户。
        $user = null;
        $userIdentifier = $requestedRefreshToken->getUserIdentifier();
        if ($userIdentifier !== null) {
            $user = $this->getUserRepository()->getUserEntity($userIdentifier);
            if (!$user instanceof UserEntityInterface) {
                throw new OAuthServerException(400, 'Invalid user.');
            }
        }
        
        // 确定最终授予的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($requestedScopes, $this->getIdentifier(), $client, $user);
        
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client, $user);
        
        // 创建更新令牌。
        $refreshToken = $this->generateRefreshToken($accessToken);
        
        // 生成认证信息。
        $credentials = $this->generateCredentials($accessToken, $refreshToken);
        
        // 撤销与更新令牌关联的访问令牌。
        $this->getAccessTokenRepository()->revokeAccessTokenEntity($requestedRefreshToken->getAccessTokenIdentifier());
        
        // 撤销更新令牌。
        $this->getRefreshTokenRepository()->revokeRefreshTokenEntity($requestedRefreshToken->getIdentifier());
        
        // 返回认证信息。
        return $credentials;
    }

    /**
     * 获取请求的更新令牌。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return RefreshTokenEntityInterface 更新令牌。
     * @throws OAuthServerException 缺少参数。
     */
    protected function getRequestedRefreshToken($request)
    {
        $requestedRefreshToken = $this->getRequestBodyParam($request, 'refresh_token');
        if ($requestedRefreshToken === null) {
            throw new OAuthServerException(400, 'Missing parameters: "refresh_token" required.');
        }
        
        $refreshToken = $this->getRefreshTokenRepository()->unserializeRefreshTokenEntity($requestedRefreshToken, $this->getRefreshTokenCryptKey());
        if (!$refreshToken instanceof RefreshTokenEntityInterface) {
            throw new OAuthServerException(401, 'Refresh token is invalid.');
        }
        
        return $refreshToken;
    }
    
    /**
     * 验证请求的更新令牌。
     * 
     * @param RefreshTokenEntityInterface $refreshToken 更新令牌。
     * @param ClientEntityInterface $client 客户端。
     * @throws OAuthServerException 令牌没有关联到当前客户端，或者令牌过期，或者令牌已撤销。
     */
    protected function validateRefreshToken(RefreshTokenEntityInterface $refreshToken, ClientEntityInterface $client)
    {
        if ($refreshToken->getClientIdentifier() != $client->getIdentifier()) {
            throw new OAuthServerException(401, 'Refresh token is not linked to client.');
        } elseif ($refreshToken->getExpires() < time()) {
            throw new OAuthServerException(401, 'Refresh token has expired.');
        } elseif ($this->getRefreshTokenRepository()->isRefreshTokenEntityRevoked($refreshToken->getIdentifier())) {
            throw new OAuthServerException(401, 'Refresh token has been revoked.');
        }
    }
}