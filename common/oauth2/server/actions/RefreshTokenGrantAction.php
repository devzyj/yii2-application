<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use common\oauth2\server\interfaces\UserEntityInterface;
use common\oauth2\server\interfaces\RefreshTokenEntityInterface;
use common\oauth2\server\interfaces\ClientEntityInterface;
use yii\web\yii\web;

/**
 * RefreshTokenGrantAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RefreshTokenGrantAction extends GrantAction
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    
        if ($this->userRepository === null) {
            throw new InvalidConfigException('The "userRepository" property must be set.');
        } elseif ($this->refreshTokenRepository === null) {
            throw new InvalidConfigException('The "refreshTokenRepository" property must be set.');
        }
    }

    /**
     * Generate user credentials.
     * 
     * @return array
     */
    public function run()
    {
        // 获取正在请求授权的客户端。
        $client = $this->getAuthorizeClient();
        
        // 获取请求的更新令牌。
        $requestedRefreshToken = $this->getRequestedRefreshToken();
        
        // 验证请求的更新令牌。
        $this->validateRefreshToken($requestedRefreshToken, $client);
        
        // 获取请求中的权限。
        $refreshTokenScopeIdentifiers = $requestedRefreshToken->getScopeIdentifiers();
        $requestedScopes = $this->getRequestedScopes(implode(self::SCOPE_SEPARATOR, $refreshTokenScopeIdentifiers));
        foreach ($requestedScopes as $scope) {
            if (in_array($scope->getIdentifier(), $refreshTokenScopeIdentifiers, true) === false) {
                throw new UnauthorizedHttpException('The requested scope is invalid.');
            }
        }
        
        // TODO 撤销与更新令牌关联的访问令牌。
        $this->getAccessTokenRepository()->revokeAccessTokenEntity();
        
        // 撤销更新令牌。
        $this->getRefreshTokenRepository()->revokeRefreshTokenEntity($requestedRefreshToken->getIdentifier());
        
        // TODO 创建访问令牌。
        $accessToken = $this->generateAccessToken($requestedScopes, $client, $requestedRefreshToken->getUserIdentifier());
        
        // 创建更新令牌。
        $refreshToken = $this->generateRefreshToken($accessToken);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($accessToken, $refreshToken);
    }

    /**
     * 获取请求的更新令牌。
     *
     * @return RefreshTokenEntityInterface 更新令牌。
     * @throws \yii\web\BadRequestHttpException 缺少参数。
     */
    protected function getRequestedRefreshToken()
    {
        $refreshToken = $this->request->getBodyParam('refresh_token');
        if ($refreshToken === null) {
            throw new BadRequestHttpException('Missing parameters: "refresh_token" required.');
        }
        
        return $this->getRefreshTokenRepository()->unserializeRefreshTokenEntity($refreshToken, $this->refreshTokenCryptKey);
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
            throw new UnauthorizedHttpException('Token is not linked to client.');
        } elseif ($refreshToken->getExpires() < time()) {
            throw new UnauthorizedHttpException('Token has expired.');
        } elseif ($this->getRefreshTokenRepository()->isRefreshTokenEntityRevoked($refreshToken->getIdentifier())) {
            throw new UnauthorizedHttpException('Token has been revoked.');
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