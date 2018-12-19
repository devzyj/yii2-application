<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\interfaces\AuthorizationCodeEntityInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\RefreshTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\authorizes\AuthorizeRequestInterface;
use devjerry\oauth2\server\exceptions\UnauthorizedClientException;
use devjerry\oauth2\server\exceptions\ForbiddenException;
use devjerry\oauth2\server\exceptions\InvalidScopeException;
use devjerry\oauth2\server\exceptions\UniqueIdentifierException;
use devjerry\oauth2\server\exceptions\ServerErrorException;

/**
 * AbstractAuthorizeGrant class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class AbstractAuthorizeGrant extends BaseObject
{
    use AuthorizeGrantPropertyTrait, RepositoryPropertyTrait, ServerRequestTrait;

    /**
     * @var string 授权码授权模式。
     */
    const AUTHORIZE_TYPE_CODE = 'code';
    
    /**
     * @var string 令牌授权模式。
     */
    const AUTHORIZE_TYPE_TOKEN = 'token';

    /**
     * @var string 授权码授予模式。
     */
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';

    /**
     * @var string 简单授予模式。
     */
    const GRANT_TYPE_IMPLICIT = 'implicit';
    
    /**
     * @var string 用户密码授予模式。
     */
    const GRANT_TYPE_PASSWORD = 'password';
    
    /**
     * @var string 客户端授予模式。
     */
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    
    /**
     * @var string 更新令牌模式。
     */
    const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';
    
    /**
     * @var string 权限范围的分隔符。
     */
    const SCOPE_SEPARATOR = ' ';

    /**
     * @var integer 生成标识的最大次数。
     */
    const GENERATE_IDENDIFIER_MAX = 10;
    
    /**
     * 使用客户端认证信息，获取客户端实例。
     * 
     * @param string $identifier 客户端标识。
     * @param string|null $secret 客户端密钥。如果为 `null`，则不验证。 
     * @return ClientEntityInterface 客户端。
     * @throws UnauthorizedClientException 客户端认证失败。
     */
    protected function getClientByCredentials($identifier, $secret = null)
    {
        $client = $this->getClientRepository()->getClientEntityByCredentials($identifier, $secret);
        if (!$client instanceof ClientEntityInterface) {
            throw new UnauthorizedClientException('Client authentication failed.');
        }
        
        return $client;
    }
    
    /**
     * 验证客户端是否允许执行指定的权限授予类型。
     * 
     * @param ClientEntityInterface $client 客户端。
     * @param string $grantType 权限授予类型。
     * @throws ForbiddenException 禁止的权限授予类型。
     */
    protected function validateClientGrantType(ClientEntityInterface $client, $grantType)
    {
        $grantTypes = $client->getGrantTypes();
        if (is_array($grantTypes) && !in_array($grantType, $grantTypes)) {
            throw new ForbiddenException('The grant type is unauthorized for this client.');
        }
    }
    
    /**
     * 验证权限。
     * 
     * @param ScopeEntityInterface[]|string[]|string $scopes 需要验证的权限标识。
     * @return ScopeEntityInterface[] 验证有效的权限。
     * @throws InvalidScopeException 无效的权限。
     */
    protected function validateScopes($scopes)
    {
        if (empty($scopes)) {
            return [];
        } elseif (is_string($scopes)) {
            $scopes = array_filter(explode(self::SCOPE_SEPARATOR, trim($scopes)), function ($scope) {
                return $scope !== '';
            });
        }
        
        $validScopes = [];
        foreach ($scopes as $scope) {
            if ($scope instanceof ScopeEntityInterface) {
                $validScopes[$scope->getIdentifier()] = $scope;
            } elseif (is_string($scope) && !isset($validScopes[$scope])) {
                $scopeEntity = $this->getScopeRepository()->getScopeEntity($scope);
                if (!$scopeEntity instanceof ScopeEntityInterface) {
                    throw new InvalidScopeException('The scope is invalid.');
                }

                $validScopes[$scope] = $scopeEntity;
            }
        }
        
        return array_values($validScopes);
    }
    
    /**
     * 生成唯一标识。
     * 
     * @param int $length 长度。
     * @return string 唯一标识。
     * @throws ServerErrorException 生成失败。
     */
    protected function generateUniqueIdentifier($length = 40)
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (\TypeError $e) {
            throw new ServerErrorException('An unexpected error has occurred.');
        } catch (\Error $e) {
            throw new ServerErrorException('An unexpected error has occurred.');
        } catch (\Exception $e) {
            throw new ServerErrorException('Could not generate a random string.');
        }
    }

    /**
     * 生成并且保存访问令牌。
     * 
     * @param ScopeEntityInterface[] $scopes 需要关联的权限。
     * @param ClientEntityInterface $client 需要关联的客户端。
     * @param UserEntityInterface $user 需要关联的用户。
     * @return AccessTokenEntityInterface 生成并且保存成功的访问令牌。
     */
    protected function generateAccessToken(array $scopes, ClientEntityInterface $client, UserEntityInterface $user = null)
    {
        $accessTokenRepository = $this->getAccessTokenRepository();
        $accessToken = $accessTokenRepository->createAccessTokenEntity();

        // 添加权限。
        foreach ($scopes as $scope) {
            $accessToken->addScopeEntity($scope);
        }

        // 设置客户端。
        $accessToken->setClientEntity($client);
        
        // 设置用户。
        if ($user) {
            $accessToken->setUserEntity($user);
        }

        // 设置过期时间。
        $duration = $client->getAccessTokenDuration();
        if ($duration === null) {
            $duration = $this->getAccessTokenDuration();
        }
        $accessToken->setExpires(time() + (int) $duration);
        
        // 生成唯一标识，并保存令牌。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成唯一标识。
            $identifier = $accessTokenRepository->generateAccessTokenUniqueIdentifier();
            if ($identifier === null) {
                $identifier = $this->generateUniqueIdentifier();
            }

            // 设置唯一标识。
            $accessToken->setIdentifier($identifier);
        
            try {
                // 保存令牌。
                $accessTokenRepository->saveAccessTokenEntity($accessToken);
                
                // 返回保存成功的令牌。
                return $accessToken;
            } catch (UniqueIdentifierException $e) {
                if ($count === 0) {
                    throw $e;
                }
            }
        }
    }

    /**
     * 生成并且保存授权码。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest 授权请求。
     * @return AuthorizationCodeEntityInterface 生成并且保存成功的访问授权码。
     */
    protected function generateAuthorizationCode(AuthorizeRequestInterface $authorizeRequest)
    {
        $authorizationCodeRepository = $this->getAuthorizationCodeRepository();
        $authorizationCode = $authorizationCodeRepository->createAuthorizationCodeEntity();

        // 设置授权码属性。
        $authorizationCode->setExpires(time() + (int) $this->getAuthorizationCodeDuration());
        $authorizationCode->setClientEntity($authorizeRequest->getClientEntity());
        $authorizationCode->setUserEntity($authorizeRequest->getUsertEntity());
        $authorizationCode->setRedirectUri($authorizeRequest->getRedirectUri());
        $authorizationCode->setCodeChallenge($authorizeRequest->getCodeChallenge());
        $authorizationCode->getCodeChallengeMethod($authorizeRequest->getCodeChallengeMethod());
        foreach ($authorizeRequest->getScopeEntities() as $scopeEntity) {
            $authorizationCode->addScopeEntity($scopeEntity);
        }
        
        // 生成唯一标识，并保存授权码。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成唯一标识。
            $identifier = $authorizationCodeRepository->generateAuthorizationCodeUniqueIdentifier();
            if ($identifier === null) {
                $identifier = $this->generateUniqueIdentifier();
            }

            // 设置唯一标识。
            $authorizationCode->setIdentifier($identifier);
        
            try {
                // 保存授权码。
                $authorizationCodeRepository->saveAuthorizationCodeEntity($authorizationCode);
                
                // 返回保存成功的授权码。
                return $authorizationCode;
            } catch (UniqueIdentifierException $e) {
                if ($count === 0) {
                    throw $e;
                }
            }
        }
    }

    /**
     * 生成并且保存更新令牌。
     *
     * @param AccessTokenEntityInterface $accessToken 访问令牌。
     * @return RefreshTokenEntityInterface 生成并且保存成功的更新令牌。
     */
    protected function generateRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $refreshTokenRepository = $this->getRefreshTokenRepository();
        $refreshToken = $refreshTokenRepository->createRefreshTokenEntity();
        
        // 设置关联的访问令牌。
        $refreshToken->setAccessTokenEntity($accessToken);

        // 设置客户端。
        $client = $accessToken->getClientEntity();
        $refreshToken->setClientEntity($client);

        // 设置用户。
        $user = $accessToken->getUserEntity();
        if ($user) {
            $refreshToken->setUserEntity($user);
        }

        // 添加权限。
        $scopes = $accessToken->getScopeEntities();
        foreach ($scopes as $scope) {
            $refreshToken->addScopeEntity($scope);
        }

        // 设置过期时间。
        $duration = $client->getRefreshTokenDuration();
        if ($duration === null) {
            $duration = $this->getRefreshTokenDuration();
        }
        $refreshToken->setExpires(time() + (int) $duration);
        
        // 生成唯一标识，并保存令牌。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成唯一标识。
            $identifier = $refreshTokenRepository->generateRefreshTokenUniqueIdentifier();
            if ($identifier === null) {
                $identifier = $this->generateUniqueIdentifier();
            }

            // 设置唯一标识。
            $refreshToken->setIdentifier($identifier);
        
            try {
                // 保存令牌。
                $refreshTokenRepository->saveRefreshTokenEntity($refreshToken);
        
                // 返回保存成功的令牌。
                return $refreshToken;
            } catch (UniqueIdentifierException $e) {
                if ($count === 0) {
                    throw $e;
                }
            }
        }
    }
    
    /**
     * 生成认证信息。
     * 
     * @param AccessTokenEntityInterface $accessToken 访问令牌。
     * @param RefreshTokenEntityInterface $refreshToken 更新令牌。
     * @return array 认证信息。
     */
    protected function generateCredentials(AccessTokenEntityInterface $accessToken, RefreshTokenEntityInterface $refreshToken = null)
    {
        // 获取访问令牌中的权限标识列表。
        $scopes = array_map(function (ScopeEntityInterface $scopeEntity) {
            return $scopeEntity->getIdentifier();
        }, $accessToken->getScopeEntities());
        
        // 认证信息中要展示的权限。
        $scope = $scopes ? implode(self::SCOPE_SEPARATOR, $scopes) : null;
        
        // 访问令牌信息。
        $credentials = [
            'token_type' => 'Bearer',
            'access_token' => $this->getAccessTokenRepository()->serializeAccessTokenEntity($accessToken, $this->getAccessTokenCryptKey()),
            'expires_in' => $accessToken->getExpires() - time(),
            'scope' => $scope,
        ];
        
        // 更新令牌信息。
        if ($refreshToken) {
            $credentials['refresh_token'] = $this->getRefreshTokenRepository()->serializeRefreshTokenEntity($refreshToken, $this->getRefreshTokenCryptKey());
            $credentials['refresh_expires_in'] = $refreshToken->getExpires() - time();
        }
        
        // 返回认证信息。
        return $credentials;
    }
}