<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

use devjerry\oauth2\server\base\AbstractAuthorizeGrant;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\exceptions\BadRequestException;
use devjerry\oauth2\server\exceptions\UserDeniedAuthorizeException;
use devjerry\oauth2\server\exceptions\OAuthServerException;

/**
 * AbstractAuthorize class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class AbstractAuthorize extends AbstractAuthorizeGrant implements AuthorizeTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function canRun($request)
    {
        $responseType = $this->getRequestQueryParam($request, 'response_type');
        return $this->getIdentifier() === $responseType;
    }

    /**
     * {@inheritdoc}
     * 
     * @throws BadRequestException 缺少参数，或者回调地址无效。
     */
    public function getAuthorizeRequest($request)
    {
        // 获取客户端标识。
        $clientId = $this->getRequestQueryParam($request, 'client_id', $this->getRequestAuthUser($request));
        if ($clientId === null) {
            throw new BadRequestException('Missing parameters: `client_id` required.');
        }
        
        // 获取客户端实例。
        $client = $this->getClientByCredentials($clientId);

        // 验证客户端是否允许执行指定的权限授予类型。
        $this->validateClientGrantType($client, $this->getGrantIdentifier());
        
        // 获取回调地址。
        $redirectUri = $this->getRequestQueryParam($request, 'redirect_uri');
        
        // 确认客户端的回调地址。
        $redirectUri = $this->ensureRedirectUri($client, $redirectUri);
        if ($redirectUri === null) {
            throw new BadRequestException('The redirect uri is invalid.');
        }

        // 获取请求的 `state`。
        $requestedState = $this->getRequestQueryParam($request, 'state');
        
        try {
            // 获取请求的权限。
            $requestedScopes = $this->getRequestedScopes($request, $this->getDefaultScopes());
        } catch (OAuthServerException $exception) {
            // 设置异常的回调地址。
            $exception->setRedirectUri($this->makeRedirectUri($redirectUri, ['state' => $requestedState]));
            throw $exception;
        }

        // 实例化授权请求。
        $authorizeRequest = new AuthorizeRequest();
        $authorizeRequest->setAuthorizeType($this);
        $authorizeRequest->setClientEntity($client);
        $authorizeRequest->setRedirectUri($redirectUri);
        $authorizeRequest->setState($requestedState);
        $authorizeRequest->setScopeEntities($requestedScopes);
        
        // 返回授权请求。
        return $authorizeRequest;
    }

    /**
     * 确认客户端的回调地址。
     *
     * @param ClientEntityInterface $client 客户端实例。
     * @param string $redirectUri 请求的回调地址。
     * @return string 回调地址。
     */
    protected function ensureRedirectUri(ClientEntityInterface $client, $redirectUri)
    {
        $clientRedirectUri = $client->getRedirectUri();
        if ($redirectUri === null) {
            if ($clientRedirectUri && is_array($clientRedirectUri)) {
                return reset($clientRedirectUri);
            } elseif ($clientRedirectUri && is_string($clientRedirectUri)) {
                return $clientRedirectUri;
            }
        } elseif (is_string($clientRedirectUri) && strcmp($clientRedirectUri, $redirectUri) === 0) {
            return $redirectUri;
        } elseif (is_array($clientRedirectUri) && in_array($redirectUri, $clientRedirectUri, true)) {
            return $redirectUri;
        }
    
        return null;
    }

    /**
     * 获取请求的权限。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @param ScopeEntityInterface[]|string[] $default 默认权限。
     * @return ScopeEntityInterface[] 权限列表。
     */
    protected function getRequestedScopes($request, array $default = null)
    {
        $requestedScopes = $this->getRequestQueryParam($request, 'scope', $default);
        return $this->validateScopes($requestedScopes);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @throws \LogicException 没有设置用户。
     * @throws UserDeniedAuthorizeException 用户拒绝授权。
     */
    public function run(AuthorizeRequestInterface $authorizeRequest)
    {
        if ($authorizeRequest->getUsertEntity() instanceof UserEntityInterface === false) {
            throw new \LogicException('An instance of UserEntityInterface should be set on the AuthorizationRequest.');
        }
        
        try {
            if (!$authorizeRequest->getApproved()) {
                // 用户拒绝授权。
                throw new UserDeniedAuthorizeException('The user denied the authorization.');
            }
            
            // 运行用户允许授权的具体方法。
            return $this->runUserAllowed($authorizeRequest);
        } catch (OAuthServerException $exception) {
            $redirectUri = $authorizeRequest->getRedirectUri();
            $state = $authorizeRequest->getState();
            
            // 设置异常的回调地址。
            $exception->setRedirectUri($this->makeRedirectUri($redirectUri, ['state' => $state]));
            throw $exception;
        }
    }
    
    /**
     * 用户允许授权的具体方法。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest 授权请求。 
     * @return string 回调地址。 
     */
    abstract protected function runUserAllowed(AuthorizeRequestInterface $authorizeRequest);
    
    /**
     * 构造回调地址。
     * 
     * @param string $uri
     * @param array $params
     * @return string
     */
    public function makeRedirectUri($uri, array $params = [])
    {
        $anchor = isset($params['#']) ? '#' . $params['#'] : '';
        unset($params['#']);
        
        $delimiter = '';
        if ($params) {
            $delimiter = strpos($uri, '?') === false ? '?' : '&';
        }
        
        return $uri . $delimiter . http_build_query($params) . $anchor;
    }
}