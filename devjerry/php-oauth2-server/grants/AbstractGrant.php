<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\base\AbstractAuthorizeGrant;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\exceptions\OAuthServerException;

/**
 * AbstractGrant class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class AbstractGrant extends AbstractAuthorizeGrant implements GrantTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function canRun(ServerRequestInterface $request)
    {
        $grantType = $this->getRequestBodyParam($request, 'grant_type');
        return $this->getIdentifier() === $grantType;
    }

    /**
     * {@inheritdoc}
     */
    public function run(ServerRequestInterface $request)
    {
        // 获取正在请求授权的客户端。
        $client = $this->getAuthorizeClient($request);

        // 验证客户端是否允许执行指定的权限授予类型。
        $this->validateClientGrantType($client, $this->getIdentifier());
        
        // 运行权限授予的具体方法。
        return $this->runGrant($request, $client);
    }
    
    /**
     * 权限授予的具体方法。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return array 认证信息。
     */
    abstract protected function runGrant(ServerRequestInterface $request, ClientEntityInterface $client);

    /**
     * 获取正在请求授权的客户端。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return ClientEntityInterface 客户端。
     * @throws OAuthServerException 缺少参数。
     */
    protected function getAuthorizeClient(ServerRequestInterface $request)
    {
        // 获取客户端认证信息。
        list ($identifier, $secret) = $this->getClientAuthCredentials($request);
        if ($identifier === null || $secret === null) {
            throw new OAuthServerException(400, 'Missing parameters: "client_id" and "client_secret" required.');
        }
    
        // 获取并返回正在授权的客户端实例。
        return $this->getClientByCredentials($identifier, $secret);
    }
    
    /**
     * 从请求的头部，或者内容中获取客户端的认证信息。
     * 优先使用请求内容中的认证信息。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return array 认证信息。第一个元素为 `client_id`，第二个元素为 `client_secret`。
     */
    protected function getClientAuthCredentials(ServerRequestInterface $request)
    {
        // 从请求头中获取。
        list ($authUser, $authPassword) = $this->getRequestAuthCredentials($request);
        
        // 从请求内容中获取。
        $identifier = $this->getRequestBodyParam($request, 'client_id', $authUser);
        $secret = $this->getRequestBodyParam($request, 'client_secret', $authPassword);
        
        // 返回客户端的认证信息。
        return [$identifier, $secret];
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
        return $this->validateScopes($requestedScopes);
    }
}