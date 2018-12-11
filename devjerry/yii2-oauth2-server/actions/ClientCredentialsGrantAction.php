<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

/**
 * ClientCredentialsGrantAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsGrantAction extends GrantAction
{
    /**
     * Generate credentials.
     * 
     * @return array
     */
    public function run()
    {
        // 获取正在请求授权的客户端。
        $client = $this->getAuthorizeClient();

        // 验证客户端是否允许使用当前的授权类型。
        $this->validateClientGrantType($client);
        
        // 获取请求中的权限。
        $requestedScopes = $this->getRequestedScopes();
        
        // 确定最终授权的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($requestedScopes, $this->getGrantType(), $client);
        
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($accessToken);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGrantType()
    {
        return self::GRANT_TYPE_CLIENT_CREDENTIALS;
    }
}