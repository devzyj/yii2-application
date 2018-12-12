<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

use devjerry\oauth2\server\interfaces\ClientRepositoryInterface;
use devjerry\oauth2\server\interfaces\ScopeRepositoryInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\exceptions\OAuthServerException;

/**
 * AbstractAuthorizeGrant class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class AbstractAuthorizeGrant
{
    use ServerRequestTrait;

    /**
     * @var string 授权码模式。
     */
    const RESPONSE_TYPE_CODE = 'code';
    
    /**
     * @var string 令牌模式。
     */
    const RESPONSE_TYPE_TOKEN = 'token';

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
     * @var ClientRepositoryInterface 客户端存储库。
     */
    private $_clientRepository;

    /**
     * @var ScopeRepositoryInterface 权限存储库。
     */
    private $_scopeRepository;
    
    /**
     * @var string[] 默认权限。
     */
    private $_defaultScopes = [];

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
     * 使用客户端认证信息，获取客户端实例。
     * 
     * @param string $identifier 客户端标识。
     * @param string|null $secret 客户端密钥。如果为 `null`，则不验证。 
     * @return ClientEntityInterface 客户端。
     * @throws OAuthServerException 客户端无效。
     */
    protected function getClientByCredentials($identifier, $secret = null)
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
     * @param string $grantType 权限授予类型。
     * @throws OAuthServerException 禁止的权限授予类型。
     */
    protected function validateClientGrantType(ClientEntityInterface $client, $grantType)
    {
        $grantTypes = $client->getGrantTypes();
        if ($grantTypes !== null && !in_array($grantType, $grantTypes)) {
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
}