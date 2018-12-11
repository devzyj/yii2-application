<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\exceptions\OAuthServerException;
use devjerry\oauth2\server\interfaces\GrantTypeInterface;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use devjerry\oauth2\server\interfaces\AuthorizationCodeRepositoryInterface;
use devjerry\oauth2\server\interfaces\ClientRepositoryInterface;
use devjerry\oauth2\server\interfaces\RefreshTokenRepositoryInterface;
use devjerry\oauth2\server\interfaces\ScopeRepositoryInterface;
use devjerry\oauth2\server\interfaces\UserRepositoryInterface;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\RefreshTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\exceptions\UniqueIdentifierException;

/**
 * AbstractGrant class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class AbstractGrant implements GrantTypeInterface
{
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
     * @var AccessTokenRepositoryInterface 访问令牌存储库。
     */
    private $_accessTokenRepository;

    /**
     * @var AuthorizationCodeRepositoryInterface 授权码存储库。
     */
    private $_authorizationCodeRepository;
    
    /**
     * @var ClientRepositoryInterface 客户端存储库。
     */
    private $_clientRepository;

    /**
     * @var RefreshTokenRepositoryInterface 更新令牌存储库。
     */
    private $_refreshTokenRepository;
    
    /**
     * @var ScopeRepositoryInterface 权限存储库。
     */
    private $_scopeRepository;

    /**
     * @var UserRepositoryInterface 用户存储库。
     */
    private $_userRepository;

    /**
     * @var mixed 访问令牌密钥。
     */
    private $_accessTokenCryptKey;
    
    /**
     * @var mixed 更新令牌密钥。
     */
    private $_refreshTokenCryptKey;
    
    /**
     * 获取访问令牌存储库。
     *
     * @return AccessTokenRepositoryInterface
     */
    public function getAccessTokenRepository()
    {
        return $this->_accessTokenRepository;
    }

    /**
     * 设置访问令牌存储库。
     *
     * @param AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository)
    {
        $this->_accessTokenRepository = $accessTokenRepository;
    }
    
    /**
     * 获取授权码存储库。
     *
     * @return AuthorizationCodeRepositoryInterface
     */
    public function getAuthorizationCodeRepository()
    {
        return $this->_authorizationCodeRepository;
    }

    /**
     * 设置授权码存储库。
     *
     * @param AuthorizationCodeRepositoryInterface $authorizationCodeRepository
     */
    public function setAuthorizationCodeRepository(AuthorizationCodeRepositoryInterface $authorizationCodeRepository)
    {
        $this->_authorizationCodeRepository = $authorizationCodeRepository;
    }
    
    /**
     * 获取客户端存储库。
     *
     * @return ClientRepositoryInterface
     */
    public function getClientRepository()
    {
        return $this->_clientRepository;
    }

    /**
     * 设置客户端存储库。
     *
     * @param ClientRepositoryInterface $clientRepository
     */
    public function setClientRepository(ClientRepositoryInterface $clientRepository)
    {
        $this->_clientRepository = $clientRepository;
    }
    
    /**
     * 获取更新令牌存储库。
     *
     * @return RefreshTokenRepositoryInterface
     */
    public function getRefreshTokenRepository()
    {
        return $this->_refreshTokenRepository;
    }

    /**
     * 设置更新令牌存储库。
     *
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function setRefreshTokenRepository(RefreshTokenRepositoryInterface $refreshTokenRepository)
    {
        $this->_refreshTokenRepository = $refreshTokenRepository;
    }
    
    /**
     * 获取权限存储库。
     *
     * @return ScopeRepositoryInterface
     */
    public function getScopeRepository()
    {
        return $this->_scopeRepository;
    }

    /**
     * 设置权限存储库。
     *
     * @param ScopeRepositoryInterface $scopeRepository
     */
    public function setScopeRepository(ScopeRepositoryInterface $scopeRepository)
    {
        $this->_scopeRepository = $scopeRepository;
    }
    
    /**
     * 获取用户存储库。
     *
     * @return UserRepositoryInterface
     */
    public function getUserRepository()
    {
        return $this->_userRepository;
    }

    /**
     * 设置用户存储库。
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function setUserRepository(UserRepositoryInterface $userRepository)
    {
        $this->_userRepository = $userRepository;
    }

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
     * {@inheritdoc}
     */
    public function canRun(ServerRequestInterface $request)
    {
        $grantType = $this->getRequestBodyParam($request, 'grant_type');
        return $this->getIdentifier() === $grantType;
    }

    /**
     * 获取请求的实体参数。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @param string $name 参数名称。
     * @param mixed $default 默认值。
     * @return null|string
     */
    protected function getRequestBodyParam(ServerRequestInterface $request, $name, $default = null)
    {
        $params = (array) $request->getParsedBody();
        return isset($params[$name]) ? $params[$name] : $default;
    }
    
    /**
     * 获取请求的查询字符串参数。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @param string $name 参数名称。
     * @param mixed $default 默认值。
     * @return null|string
     */
    protected function getRequestQueryParam(ServerRequestInterface $request, $name, $default = null)
    {
        $params = (array) $request->getQueryParams();
        return isset($params[$name]) ? $params[$name] : $default;
    }
    
    /**
     * 使用请求的授权头检索 HTTP 基本身份验证凭据。
     * 返回数组的第一个索引是用户名，第二个是密码。
     * 如果报头不存在，或者是无效的 HTTP 基本报头，则返回 [null, null]。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return string[]|null[]
     */
    protected function getRequestAuthCredentials(ServerRequestInterface $request)
    {
        $authorization = $request->getHeader('Authorization');
        if (empty($authorization)) {
            return [null, null];
        }
        
        $header = reset($authorization);
        if (strpos($header, 'Basic ') !== 0) {
            return [null, null];
        }
    
        if (!($decoded = base64_decode(substr($header, 6)))) {
            return [null, null];
        }
    
        if (strpos($decoded, ':') === false) {
            return [null, null]; // HTTP Basic header without colon isn't valid
        }
    
        return explode(':', $decoded, 2);
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
     * 验证客户端是否允许使用当前的授权类型。
     * 
     * @param ClientEntityInterface $client 客户端。
     * @throws OAuthServerException 禁止的授权类型。
     */
    protected function validateClientGrantType(ClientEntityInterface $client)
    {
        $grantTypes = $client->getGrantTypes();
        if ($grantTypes !== null && !in_array($this->getIdentifier(), $grantTypes)) {
            throw new OAuthServerException(403, 'The grant type is unauthorized for this client.');
        }
    }

    /**
     * 获取请求的权限。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @param string|string[] $default 默认权限。多个权限可以是数组，也可以是以 [[SELF::SCOPE_SEPARATOR]] 分隔的字符串。
     * @return ScopeEntityInterface[] 权限列表。
     */
    protected function getRequestedScopes(ServerRequestInterface $request, $default = null)
    {
        $requestedScopes = $this->getRequestBodyParam($request, 'scope', $default);
        if ($requestedScopes) {
            if (!is_array($requestedScopes)) {
                $requestedScopes = array_filter(explode(self::SCOPE_SEPARATOR, trim($requestedScopes)), function ($scope) {
                    return $scope !== '';
                });
            }
            
            return $this->validateScopes($requestedScopes);
        }
        
        return [];
    }
    
    /**
     * 验证权限。
     * 
     * @param string[] $scopes 需要验证的权限标识。
     * @return ScopeEntityInterface[] 验证有效的权限。
     * @throws OAuthServerException 权限无效。
     */
    protected function validateScopes(array $scopes)
    {
        $validScopes = [];
        foreach ($scopes as $scope) {
            if (!isset($validScopes[$scope])) {
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
        $accessToken = $this->getAccessTokenRepository()->createAccessTokenEntity();

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
        $accessToken->setExpires(time() + $client->getAccessTokenDuration());
        
        // 生成唯一标识，并保存令牌。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成并设置唯一标识。
            $accessToken->setIdentifier($this->generateUniqueIdentifier());
            
            try {
                // 保存令牌。
                $this->getAccessTokenRepository()->saveAccessTokenEntity($accessToken);
                
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
        $refreshToken = $this->getRefreshTokenRepository()->createRefreshTokenEntity();
        
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
        $refreshToken->setExpires(time() + $client->getRefreshTokenDuration());

        // 生成唯一标识，并保存令牌。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成并设置唯一标识。
            $refreshToken->setIdentifier($this->generateUniqueIdentifier());
        
            try {
                // 保存令牌。
                $this->getRefreshTokenRepository()->saveRefreshTokenEntity($refreshToken);
        
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
     * 生成唯一标识。
     * 
     * @param int $length 长度。
     * @return string 唯一标识。
     * @throws OAuthServerException 生成失败。
     */
    protected function generateUniqueIdentifier($length = 40)
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (\TypeError $e) {
            throw new OAuthServerException(500, 'An unexpected error has occurred.');
        } catch (\Error $e) {
            throw new OAuthServerException(500, 'An unexpected error has occurred.');
        } catch (\Exception $e) {
            throw new OAuthServerException(500, 'Could not generate a random string.');
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