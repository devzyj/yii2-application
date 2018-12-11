<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use common\oauth2\server\interfaces\RefreshTokenEntityInterface;
use common\oauth2\server\interfaces\ClientEntityInterface;

/**
 * RefreshTokenGrantAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RefreshTokenGrantAction extends GrantAction
{
    /**
     * Generate credentials.
     * 
     * @return array
     */
    public function run()
    {
        // 获取正在请求授权的客户端。
        $client = $this->getAuthorizeClient();

        // 验证客户端是否允许使用当前的授权类型。
        $this->validateClientGrantType($client);
        
        // 获取请求的更新令牌。
        $requestedRefreshToken = $this->getRequestedRefreshToken();
        
        // 验证请求的更新令牌。
        $this->validateRefreshToken($requestedRefreshToken, $client);
        
        // 获取请求中的权限。
        $refreshTokenScopeIdentifiers = $requestedRefreshToken->getScopeIdentifiers();
        $requestedScopes = $this->getRequestedScopes($refreshTokenScopeIdentifiers);
        foreach ($requestedScopes as $scope) {
            if (in_array($scope->getIdentifier(), $refreshTokenScopeIdentifiers, true) === false) {
                throw new UnauthorizedHttpException('The requested scope is invalid.');
            }
        }
        
        // 获取与更新令牌关联的用户。
        $user = $this->getUserRepository()->getUserEntity($requestedRefreshToken->getUserIdentifier());

        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($requestedScopes, $client, $user);
        
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
     * @return RefreshTokenEntityInterface 更新令牌。
     * @throws BadRequestHttpException 缺少参数。
     */
    protected function getRequestedRefreshToken()
    {
        $requestedRefreshToken = $this->request->getBodyParam('refresh_token');
        if ($requestedRefreshToken === null) {
            throw new BadRequestHttpException('Missing parameters: "refresh_token" required.');
        }
        
        $refreshToken = $this->getRefreshTokenRepository()->unserializeRefreshTokenEntity($requestedRefreshToken, $this->refreshTokenCryptKey);
        if (!$refreshToken instanceof RefreshTokenEntityInterface) {
            throw new UnauthorizedHttpException('Refresh token is invalid.');
        }
        
        return $refreshToken;
    }
    
    /**
     * 验证请求的更新令牌。
     * 
     * @param RefreshTokenEntityInterface $refreshToken
     * @param ClientEntityInterface $client
     * @throws UnauthorizedHttpException 令牌没有关联到当前客户端，或者令牌过期，或者令牌已撤销。
     */
    protected function validateRefreshToken(RefreshTokenEntityInterface $refreshToken, ClientEntityInterface $client)
    {
        if ($refreshToken->getClientIdentifier() != $client->getIdentifier()) {
            throw new UnauthorizedHttpException('Refresh token is not linked to client.');
        } elseif ($refreshToken->getExpires() < time()) {
            throw new UnauthorizedHttpException('Refresh token has expired.');
        } elseif ($this->getRefreshTokenRepository()->isRefreshTokenEntityRevoked($refreshToken->getIdentifier())) {
            throw new UnauthorizedHttpException('Refresh token has been revoked.');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGrantType()
    {
        return self::GRANT_TYPE_REFRESH_TOKEN;
    }
}