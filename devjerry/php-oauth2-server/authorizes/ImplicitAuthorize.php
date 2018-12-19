<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

/**
 * ImplicitAuthorize class.
 *
 * ```php
 * use devjerry\oauth2\server\authorizes\ImplicitAuthorize;
 * 
 * // 实例化对像。
 * $implicitAuthorize = new ImplicitAuthorize([
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
class ImplicitAuthorize extends AbstractAuthorize
{
    /**
     * {@inheritdoc}
     */
    protected function getIdentifier()
    {
        return self::AUTHORIZE_TYPE_TOKEN;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGrantIdentifier()
    {
        return self::GRANT_TYPE_IMPLICIT;
    }

    /**
     * {@inheritdoc}
     */
    public function canRun($request)
    {
        if ($this->getAccessTokenRepository() === null) {
            throw new \LogicException('The `accessTokenRepository` property must be set.');
        }
        
        return parent::canRun($request);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function runUserAllowed(AuthorizeRequestInterface $authorizeRequest)
    {
        $client = $authorizeRequest->getClientEntity();
        $user = $authorizeRequest->getUsertEntity();
        $scopes = $authorizeRequest->getScopeEntities();
        
        // 确定最终授予的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($scopes, $this->getGrantIdentifier(), $client, $user);

        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client, $user);

        // 生成认证信息。
        $credentials = $this->generateCredentials($accessToken);
        $credentials['state'] = $authorizeRequest->getState();
        
        // 返回授权成功的回调地址。
        return $this->makeRedirectUri($authorizeRequest->getRedirectUri(), [
            '#' => http_build_query($credentials),
        ]);
    }
}