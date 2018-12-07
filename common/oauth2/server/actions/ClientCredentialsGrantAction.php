<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

use Yii;

/**
 * ClientCredentialsGrantAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsGrantAction extends GrantAction
{
    /**
     * Generate client credentials.
     * 
     * @return array
     */
    public function run()
    {
        // 获取客户端认证信息。
        list ($identifier, $secret) = $this->getClientAuthCredentials();
        
        // 获取客户端实例。
        $client = $this->getClientByCredentials($identifier, $secret);
        
        // 验证客户端是否允许使用当前的授权类型。
        $this->validateClientGrantType($client);
        
        // 获取请求中的权限。
        $requestedScopes = $this->getRequestedScopes();
        
        // 确定最终授权的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalize($requestedScopes, $this->getGrantType(), $client);
        
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