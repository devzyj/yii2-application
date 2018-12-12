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
    public function canRun(ServerRequestInterface $request)
    {
        $responseType = $this->getRequestQueryParam($request, 'response_type');
        return $this->getIdentifier() === $responseType;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizeRequest(ServerRequestInterface $request)
    {
        // 获取客户端标识。
        $clientId = $this->getRequestQueryParam($request, 'client_id', $this->getRequestAuthUser($request));
        if ($clientId === null) {
            throw new OAuthServerException(400, 'Missing parameters: "client_id" required.');
        }
        
        // 获取客户端实例。
        $client = $this->getClientByCredentials($clientId);
        
        // 验证客户端是否允许使用当前的权限授予类型。
        $this->validateClientGrantType($client, $this->getGrantIdentifier());
        
        // 获取回调地址。
        $redirectUri = $this->getRequestQueryParam($request, 'redirect_uri');
        
        // 验证回调地址。
        $redirectUri = $this->ensureRedirectUri($client, $redirectUri);
        if ($redirectUri === null) {
            throw new OAuthServerException(400, 'Redirect uri is invalid.');
        }
        
        // 获取请求中的权限。
        $requestedScopes = $this->getRequestedScopes($request, $this->getDefaultScopes());

        // 获取请求的 `state`。
        $requestedState = $this->getRequestQueryParam($request, 'state');
        
        // 实例化授权请求。
        /* @var $authorizeRequest AuthorizeRequestInterface  */
        //$authorizeRequest = '';
        $authorizeRequest->setAuthorizeType($this);
        $authorizeRequest->setClientEntity($client);
        $authorizeRequest->setRedirectUri($redirectUri);
        $authorizeRequest->setState($requestedState);
        $authorizeRequest->setScopeEntities($requestedScopes);
        
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
        } elseif (is_array($clientRedirectUri) && in_array($redirectUri, $clientRedirectUri, true)) {
            return $redirectUri;
        } elseif (is_string($clientRedirectUri) && strcmp($clientRedirectUri, $redirectUri) === 0) {
            return $redirectUri;
        }
        
        return null;
    }
}