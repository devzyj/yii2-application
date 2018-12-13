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
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ImplicitAuthorize extends AbstractAuthorize
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::AUTHORIZE_TYPE_TOKEN;
    }

    /**
     * {@inheritdoc}
     */
    public function getGrantIdentifier()
    {
        return self::GRANT_TYPE_IMPLICIT;
    }
    
    /**
     * {@inheritdoc}
     */
    public function runUserAllowed(AuthorizeRequestInterface $authorizeRequest)
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