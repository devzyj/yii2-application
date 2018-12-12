<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;

/**
 * ClientCredentialsGrant class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsGrant extends AbstractGrant
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::GRANT_TYPE_CLIENT_CREDENTIALS;
    }

    /**
     * {@inheritdoc}
     */
    public function run(ServerRequestInterface $request)
    {
        // 获取正在请求授权的客户端。
        $client = $this->getAuthorizeClient($request);

        // 验证客户端是否允许使用当前的权限授予类型。
        $this->validateClientGrantType($client);
        
        // 获取请求中的权限。
        $requestedScopes = $this->getRequestedScopes($request, $this->ensureDefaultScopes($client));
        
        // 确定最终授予的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($requestedScopes, $this->getIdentifier(), $client);
        
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($accessToken);
    }
}