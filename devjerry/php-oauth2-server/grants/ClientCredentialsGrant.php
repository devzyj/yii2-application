<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\interfaces\ClientEntityInterface;

/**
 * ClientCredentialsGrant class.
 *
 * ```php
 * use devjerry\oauth2\server\grants\ClientCredentialsGrant;
 * 
 * // 实例化对像。
 * $clientCredentialsGrant = new ClientCredentialsGrant([
 *     'accessTokenRepository' => new AccessTokenRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'defaultScopes' => ['basic', 'basic2'], // 默认权限。
 *     'accessTokenDuration' => 3600, // 访问令牌持续 1 小时。
 *     'accessTokenCryptKey' => [
 *         'privateKey' => '/path/to/privateKey', // 访问令牌的私钥路径。
 *         'passphrase' => null, // 访问令牌的私钥密码。没有密码可以为 `null`。
 *     ],
 *     //'accessTokenCryptKey' => 'string key', // 字符串密钥。
 * ]);
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsGrant extends AbstractGrant
{
    /**
     * {@inheritdoc}
     */
    protected function getIdentifier()
    {
        return self::GRANT_TYPE_CLIENT_CREDENTIALS;
    }

    /**
     * {@inheritdoc}
     */
    protected function runGrant($request, ClientEntityInterface $client)
    {
        // 获取默认权限。
        $defaultScopes = $client->getDefaultScopeEntities();
        if (!is_array($defaultScopes)) {
            $defaultScopes = $this->getDefaultScopes();
        }
        
        // 获取请求的权限。
        $requestedScopes = $this->getRequestedScopes($request, $defaultScopes);
        
        // 确定最终授予的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($requestedScopes, $this->getIdentifier(), $client);
        
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($accessToken);
    }
}