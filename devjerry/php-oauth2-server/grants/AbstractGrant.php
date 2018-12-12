<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\exceptions\OAuthServerException;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\RefreshTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\exceptions\UniqueIdentifierException;
use devjerry\oauth2\server\base\RepositoryTrait;
use devjerry\oauth2\server\base\ServerRequestTrait;
use devjerry\oauth2\server\base\GenerateUniqueIdentifierTrait;

/**
 * AbstractGrant class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class AbstractGrant implements GrantTypeInterface
{
    use RepositoryTrait, ServerRequestTrait, GenerateUniqueIdentifierTrait;
    
    /**
     * @var string 授权码模式。
     */
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    
    /**
     * @var string 用户密码模式。
     */
    const GRANT_TYPE_PASSWORD = 'password';
    
    /**
     * @var string 客户端模式。
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
     * @var mixed 访问令牌密钥。
     */
    private $_accessTokenCryptKey;
    
    /**
     * @var mixed 更新令牌密钥。
     */
    private $_refreshTokenCryptKey;
    
    /**
     * @var integer 访问令牌持续时间（秒）。
     */
    private $_accessTokenDuration;

    /**
     * @var integer 更新令牌持续时间（秒）。
     */
    private $_refreshTokenDuration;
    
    /**
     * @var string[] 默认权限。
     */
    private $_defaultScopes = [];
    
    /**
     * 获取访问令牌密钥。
     *
     * @return mixed
     */
    public function getAccessTokenCryptKey()
    {
        return $this->_accessTokenCryptKey;
    }
    
    /**
     * 设置访问令牌密钥。
     *
     * @param mixed $key
     */
    public function setAccessTokenCryptKey($key)
    {
        $this->_accessTokenCryptKey = $key;
    }

    /**
     * 获取更新令牌密钥。
     *
     * @return mixed
     */
    public function getRefreshTokenCryptKey()
    {
        return $this->_refreshTokenCryptKey;
    }
    
    /**
     * 设置更新令牌密钥。
     *
     * @param mixed $key
     */
    public function setRefreshTokenCryptKey($key)
    {
        $this->_refreshTokenCryptKey = $key;
    }

    /**
     * 获取默认权限。
     *
     * @return string[]
     */
    public function getDefaultScopes()
    {
        return $this->_defaultScopes;
    }
    
    /**
     * 设置默认权限。
     *
     * @param string[] $scopes
     */
    public function setDefaultScopes(array $scopes)
    {
        $this->_defaultScopes = $scopes;
    }

    /**
     * 获取访问令牌持续时间。
     *
     * @return integer 持续的秒数。
     */
    public function getAccessTokenDuration()
    {
        return $this->_accessTokenDuration;
    }
    
    /**
     * 设置访问令牌持续时间。
     * 
     * @param integer $duration 以秒为单位的持续时间。
     */
    public function setAccessTokenDuration($duration)
    {
        $this->_accessTokenDuration = $duration;
    }

    /**
     * 获取更新令牌持续时间。
     *
     * @return integer 持续的秒数。
     */
    public function getRefreshTokenDuration()
    {
        return $this->_refreshTokenDuration;
    }
    
    /**
     * 设置更新令牌持续时间。
     *
     * @param integer $duration 以秒为单位的持续时间。
     */
    public function setRefreshTokenDuration($duration)
    {
        $this->_refreshTokenDuration = $duration;
    }
    
    /**
     * {@inheritdoc}
     */
    public function canRun(ServerRequestInterface $request)
    {
        $grantType = $this->getRequestBodyParam($request, 'grant_type');
        return $this->getIdentifier() === $grantType;
    }

    /**
     * 获取正在请求授权的客户端。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return ClientEntityInterface 客户端。
     */
    protected function getAuthorizeClient(ServerRequestInterface $request)
    {
        // 获取客户端认证信息。
        list ($identifier, $secret) = $this->getClientAuthCredentials($request);
    
        // 获取并返回正在授权的客户端实例。
        return $this->getClientByCredentials($identifier, $secret);
    }
    
    /**
     * 从请求的头部，或者内容中获取客户端的认证信息。
     * 优先使用请求内容中的认证信息。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return array 认证信息。第一个元素为 `client_id`，第二个元素为 `client_secret`。
     * @throws OAuthServerException 缺少参数。
     */
    protected function getClientAuthCredentials(ServerRequestInterface $request)
    {
        // 从请求头中获取。
        list ($authUser, $authPassword) = $this->getRequestAuthCredentials($request);
        
        // 从请求内容中获取。
        $identifier = $this->getRequestBodyParam($request, 'client_id', $authUser);
        $secret = $this->getRequestBodyParam($request, 'client_secret', $authPassword);
        if ($identifier === null) {
            throw new OAuthServerException(400, 'Missing parameters: "client_id" required.');
        }
        
        return [$identifier, $secret];
    }
    
    /**
     * 使用客户端认证信息，获取客户端实例。
     * 
     * @param string $identifier 客户端标识。
     * @param string $secret 客户端密钥。
     * @return ClientEntityInterface 客户端。
     * @throws OAuthServerException 客户端无效。
     */
    protected function getClientByCredentials($identifier, $secret)
    {
        $client = $this->getClientRepository()->getClientEntityByCredentials($identifier, $secret);
        if (!$client instanceof ClientEntityInterface) {
            throw new OAuthServerException(401, 'Client authentication failed.');
        }
        
        return $client;
    }
    
    /**
     * 验证客户端是否允许使用当前的权限授予类型。
     * 
     * @param ClientEntityInterface $client 客户端。
     * @throws OAuthServerException 禁止的权限授予类型。
     */
    protected function validateClientGrantType(ClientEntityInterface $client)
    {
        $grantTypes = $client->getGrantTypes();
        if ($grantTypes !== null && !in_array($this->getIdentifier(), $grantTypes)) {
            throw new OAuthServerException(403, 'The grant type is unauthorized for this client.');
        }
    }
    
    /**
     * 通过客户端或者用户，确定最终使用的默认权限。
     * 
     * @param ClientEntityInterface|UserEntityInterface $entity 客户端或者用户实例。
     * @return ScopeEntityInterface[]|string[] 权限实例列表，或者权限标识列表。
     * @see ClientEntityInterface::getDefaultScopeEntities()
     * @see UserEntityInterface::getDefaultScopeEntities()
     */
    protected function ensureDefaultScopes($entity)
    {
        $entityDefaultScopes = $entity->getDefaultScopeEntities();
        if (is_array($entityDefaultScopes)) {
            return $entityDefaultScopes;
        }
        
        return $this->getDefaultScopes();
    }

    /**
     * 获取请求的权限。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @param ScopeEntityInterface[]|string[] $default 默认权限。
     * @return ScopeEntityInterface[] 权限列表。
     */
    protected function getRequestedScopes(ServerRequestInterface $request, array $default = null)
    {
        $requestedScopes = $this->getRequestBodyParam($request, 'scope', $default);
        if (is_string($requestedScopes)) {
            $requestedScopes = array_filter(explode(self::SCOPE_SEPARATOR, trim($requestedScopes)), function ($scope) {
                return $scope !== '';
            });
        }
        
        if (is_array($requestedScopes)) {
            return $this->validateScopes($requestedScopes);
        }
        
        return [];
    }
    
    /**
     * 验证权限。
     * 
     * @param ScopeEntityInterface[]|string[] $scopes 需要验证的权限标识。
     * @return ScopeEntityInterface[] 验证有效的权限。
     * @throws OAuthServerException 权限无效。
     */
    protected function validateScopes(array $scopes)
    {
        $validScopes = [];
        foreach ($scopes as $scope) {
            if ($scope instanceof ScopeEntityInterface) {
                $validScopes[$scope->getIdentifier()] = $scope;
            } elseif (is_string($scope) && !isset($validScopes[$scope])) {
                $scopeEntity = $this->getScopeRepository()->getScopeEntity($scope);
                if (!$scopeEntity instanceof ScopeEntityInterface) {
                    throw new OAuthServerException('Scope is invalid.');
                }

                $validScopes[$scope] = $scopeEntity;
            }
        }
        
        return array_values($validScopes);
    }

    /**
     * 生成并且保存访问令牌。
     * 
     * @param ScopeEntityInterface[] $scopes 需要关联的权限。
     * @param ClientEntityInterface $client 需要关联的客户端。
     * @param UserEntityInterface $user 需要关联的用户。
     * @return AccessTokenEntityInterface 生成并且保存成功的访问令牌。
     * @throws UniqueIdentifierException 保存令牌时唯一标识重复。
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
     * 生成并且保存更新令牌。
     *
     * @param AccessTokenEntityInterface $accessToken 访问令牌。
     * @return RefreshTokenEntityInterface 生成并且保存成功的更新令牌。
     * @throws UniqueIdentifierException 保存令牌时唯一标识重复。
     */
    protected function generateRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $refreshTokenRepository = $this->getRefreshTokenRepository();
        $refreshToken = $refreshTokenRepository->createRefreshTokenEntity();
        
        // 设置关联的访问令牌。
        $refreshToken->setAccessTokenEntity($accessToken);

        // 设置客户端。
        $client = $accessToken->getClientEntity();
        $refreshToken->setClientIdentifier($client->getIdentifier());

        // 设置用户。
        $user = $accessToken->getUserEntity();
        if ($user) {
            $refreshToken->setUserIdentifier($user->getIdentifier());
        }

        // 添加权限。
        $scopes = $accessToken->getScopeEntities();
        foreach ($scopes as $scope) {
            $refreshToken->addScopeIdentifier($scope->getIdentifier());
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
        $scopes = [];
        foreach ($accessToken->getScopeEntities() as $scopeEntity) {
            $scopes[] = $scopeEntity->getIdentifier();
        }

        // 认证信息中要展示的权限。
        $scope = null;
        if ($scopes) {
            $scope = implode(self::SCOPE_SEPARATOR, $scopes);
        }
        
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