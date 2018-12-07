<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use common\oauth2\server\interfaces\UserEntityInterface;
use common\oauth2\server\interfaces\RefreshTokenEntityInterface;

/**
 * RefreshTokenGrantAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RefreshTokenGrantAction extends GrantAction
{
    /**
     * @var CryptKey 加密更新令牌的密钥。
     */
    public $encryptionKey;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    
        if ($this->userRepository === null) {
            throw new InvalidConfigException('The "userRepository" property must be set.');
        } elseif ($this->refreshTokenRepository === null) {
            throw new InvalidConfigException('The "refreshTokenRepository" property must be set.');
        } elseif ($this->encryptionKey === null) {
            throw new InvalidConfigException('The "encryptionKey" property must be set.');
        }
    }

    /**
     * Generate user credentials.
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
        
        // 获取请求的更新令牌。
        $requestedRefreshToken = $this->getRequestedRefreshToken();
        
        // 解密更新令牌。
        $requestedRefreshToken = $this->decryptRefreshToken();
        
        // 验证请求的更新令牌。
        $this->validateRequestedRefreshToken($requestedRefreshToken);
        
        
        
        
        
        // 获取用户实例。
        $user = $this->getUserByCredentials($username, $password);
        
        // 获取请求中的权限。
        $requestedScopes = $this->getRequestedScopes();
        
        // 确定最终授权的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalize($requestedScopes, $this->getGrantType(), $client, $user);
        
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client, $user);
        
        // 创建更新令牌。
        $refreshToken = $this->generateRefreshToken($accessToken);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($accessToken, $refreshToken);
    }

    /**
     * 获取请求的更新令牌。
     *
     * @return RefreshTokenEntityInterface 更新令牌。
     * @throws \yii\web\BadRequestHttpException 缺少参数。
     * @throws \yii\web\UnauthorizedHttpException 更新令牌不能解密。
     */
    protected function getRequestedRefreshToken()
    {
        $refreshToken = $this->request->getBodyParam('refresh_token');
        if ($refreshToken === null) {
            throw new BadRequestHttpException('Missing parameters: "refresh_token" required.');
        }
        
        try {
            $refreshToken = $this->decrypt($refreshToken);
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Invalid refresh token.', 0, $e);
        }
    
        return $refreshToken;
    }
    
    /**
     * 验证请求的更新令牌。
     * 
     * @param RefreshTokenEntityInterface $requestedRefreshToken
     */
    protected function validateRequestedRefreshToken(RefreshTokenEntityInterface $requestedRefreshToken)
    {
        
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
        if (empty($user)) {
            throw new UnauthorizedHttpException('User authentication failed.');
        } elseif (!$user instanceof UserEntityInterface) {
            throw new InvalidConfigException(get_class($user) . ' does not implement UserEntityInterface.');
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