<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\actions;

use Yii;

/**
 * ClientCredentialsAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsAction extends Action
{
    /**
     * Generate client credentials.
     * 
     * @return array
     */
    public function run()
    {
        $request = Yii::$app->getRequest();
        
        // 获取 `client_id` 和 `client_secret`。
        list ($identifier, $secret) = $this->getClientAuthCredentials($request);
        
        // 获取客户端实例。
        $client = $this->getClient($identifier);
        
        // 验证客户端密钥。
        $this->validateClientSecret($client, $secret);
        
        // 验证客户端是否允许使用当前的授权类型。
        $this->validateClientGrantType($client);
        
        // 获取请求中的权限。
        $requestedScopes = $this->getRequestedScopes($request);
        
        // 确定最终授权的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalize($requestedScopes, $this->getGrantType(), $client);
        //print_r($scopes);exit();
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($requestedScopes, $accessToken);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGrantType()
    {
        return 'client_credentials';
    }
}