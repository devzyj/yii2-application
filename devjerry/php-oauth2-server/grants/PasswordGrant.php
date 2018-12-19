<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\exceptions\BadRequestException;
use devjerry\oauth2\server\exceptions\UnauthorizedUserException;

/**
 * PasswordGrant class.
 *
 * ```php
 * use devjerry\oauth2\server\grants\PasswordGrant;
 * 
 * // 实例化对像。
 * $passwordGrant = new PasswordGrant([
 *     'accessTokenRepository' => new AccessTokenRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'refreshTokenRepository' => new RefreshTokenRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'userRepository' => new UserRepository(),
 *     'defaultScopes' => ['basic', 'basic2'], // 默认权限。
 *     'accessTokenDuration' => 3600, // 访问令牌持续 1 小时。
 *     'accessTokenCryptKey' => [
 *         'privateKey' => '/path/to/privateKey', // 访问令牌的私钥路径。
 *         'passphrase' => null, // 访问令牌的私钥密码。没有密码可以为 `null`。
 *     ],
 *     //'accessTokenCryptKey' => 'string key', // 字符串密钥。
 *     'refreshTokenDuration' => 2592000, // 更新令牌持续 30 天。
 *     'refreshTokenCryptKey' => [
 *         'ascii' => 'def0000086937b.....', // 使用 `vendor/bin/generate-defuse-key` 生成的字符串。
 *         //'path' => '/path/to/asciiFile', // 保存了 `vendor/bin/generate-defuse-key` 生成的字符串的文件路径。
 *         //'password' => 'string key', // 字符串密钥。
 *     ],
 * ]);
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class PasswordGrant extends AbstractGrant
{
    /**
     * {@inheritdoc}
     */
    protected function getIdentifier()
    {
        return self::GRANT_TYPE_PASSWORD;
    }

    /**
     * {@inheritdoc}
     */
    public function canRun($request)
    {
        if ($this->getUserRepository() === null) {
            throw new \LogicException('The `userRepository` property must be set.');
        } elseif ($this->getRefreshTokenRepository() === null) {
            throw new \LogicException('The `refreshTokenRepository` property must be set.');
        }
    
        return parent::canRun($request);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function runGrant($request, ClientEntityInterface $client)
    {
        // 获取正在请求授权的用户。
        $user = $this->getAuthorizeUser($request);

        // 获取默认权限。
        $defaultScopes = $user->getDefaultScopeEntities();
        if (!is_array($defaultScopes)) {
            $defaultScopes = $this->getDefaultScopes();
        }
        
        // 获取请求的权限。
        $requestedScopes = $this->getRequestedScopes($request, $defaultScopes);
        
        // 确定最终授予的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($requestedScopes, $this->getIdentifier(), $client, $user);
        
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
     * @param ServerRequestInterface $request 服务器请求。
     * @return UserEntityInterface 用户实例。
     * @throws BadRequestException 缺少参数。
     */
    protected function getAuthorizeUser($request)
    {
        // 获取用户的认证信息。
        $username = $this->getRequestBodyParam($request, 'username');
        $password = $this->getRequestBodyParam($request, 'password');
        if ($username === null || $password === null) {
            throw new BadRequestException('Missing parameters: `username` and `password` required.');
        }
        
        // 获取并返回用户实例。
        return $this->getUserByCredentials($username, $password);
    }

    /**
     * 使用用户认证信息，获取用户实例。
     *
     * @param string $username 用户名。
     * @param string $password 用户密码。
     * @return UserEntityInterface 用户实例。
     * @throws UnauthorizedUserException 用户认证失败。
     */
    protected function getUserByCredentials($username, $password)
    {
        $user = $this->getUserRepository()->getUserEntityByCredentials($username, $password);
        if (!$user instanceof UserEntityInterface) {
            throw new UnauthorizedUserException('User authentication failed.');
        }
        
        return $user;
    }
}