<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use common\oauth2\server\interfaces\UserEntityInterface;

/**
 * PasswordGrantAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class PasswordGrantAction extends GrantAction
{
    /**
     * Generate user credentials.
     * 
     * @return array
     */
    public function run()
    {
        // 获取正在请求授权的客户端。
        $client = $this->getAuthorizeClient();

        // 验证客户端是否允许使用当前的授权类型。
        $this->validateClientGrantType($client);
        
        // 获取正在请求授权的用户。
        $user = $this->getAuthorizeUser();
        
        // 获取请求中的权限。
        $requestedScopes = $this->getRequestedScopes();
        
        // 确定最终授权的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($requestedScopes, $this->getGrantType(), $client, $user);
        
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client, $user);
        
        // 创建更新令牌。
        $refreshToken = $this->generateRefreshToken($accessToken);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($accessToken, $refreshToken);
    }
    
    /**
     * 获取正在请求授权的用户。
     *
     * @return UserEntityInterface
     */
    protected function getAuthorizeUser()
    {
        // 获取用户的认证信息。
        list ($username, $password) = $this->getUserAuthCredentials();
        
        // 获取用户实例。
        return $this->getUserByCredentials($username, $password);
    }

    /**
     * 获取用户的认证信息。
     *
     * @return array 认证信息。第一个元素为 `username`，第二个元素为 `password`。
     * @throws BadRequestHttpException 缺少参数。
     */
    protected function getUserAuthCredentials()
    {
        $username = $this->request->getBodyParam('username');
        $password = $this->request->getBodyParam('password');
        if ($username === null || $password === null) {
            throw new BadRequestHttpException('Missing parameters: "username" and "password" required.');
        }
    
        return [$username, $password];
    }

    /**
     * 使用用户认证信息，获取用户实例。
     *
     * @param string $username 用户名。
     * @param string $password 用户密码。
     * @return UserEntityInterface 用户实例。
     */
    protected function getUserByCredentials($username, $password)
    {
        $user = $this->getUserRepository()->getUserEntityByCredentials($username, $password);
        if (!$user instanceof UserEntityInterface) {
            throw new UnauthorizedHttpException('User authentication failed.');
        }
        
        return $user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGrantType()
    {
        return self::GRANT_TYPE_PASSWORD;
    }
}