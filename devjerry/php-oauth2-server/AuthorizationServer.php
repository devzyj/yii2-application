<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server;

use devjerry\oauth2\server\base\BaseObject;
use devjerry\oauth2\server\base\AuthorizeGrantPropertyTrait;
use devjerry\oauth2\server\base\RepositoryPropertyTrait;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\authorizes\AuthorizeTypeInterface;
use devjerry\oauth2\server\authorizes\CodeAuthorize;
use devjerry\oauth2\server\authorizes\ImplicitAuthorize;
use devjerry\oauth2\server\authorizes\AuthorizeRequestInterface;
use devjerry\oauth2\server\grants\GrantTypeInterface;
use devjerry\oauth2\server\grants\AuthorizationCodeGrant;
use devjerry\oauth2\server\grants\ClientCredentialsGrant;
use devjerry\oauth2\server\grants\PasswordGrant;
use devjerry\oauth2\server\grants\RefreshTokenGrant;
use devjerry\oauth2\server\exceptions\UnsupportedAuthTypeException;
use devjerry\oauth2\server\exceptions\UserDeniedAuthorizeException;

/**
 * AuthorizationServer class.
 * 
 * ```php
 * use devjerry\oauth2\server\AuthorizationServer;
 * use devjerry\oauth2\server\authorizes\CodeAuthorize;
 * use devjerry\oauth2\server\authorizes\ImplicitAuthorize;
 * use devjerry\oauth2\server\grants\AuthorizationCodeGrant;
 * use devjerry\oauth2\server\grants\ClientCredentialsGrant;
 * use devjerry\oauth2\server\grants\PasswordGrant;
 * use devjerry\oauth2\server\grants\RefreshTokenGrant;
 * 
 * // 默认权限。
 * $defaultScopes = ['basic', 'basic2'];
 * // 访问令牌持续 1 小时。
 * $accessTokenDuration = 3600;
 * // 访问令牌密钥。
 * //$accessTokenCryptKey => 'string key', // 字符串密钥。
 * $accessTokenCryptKey => [
 *     'privateKey' => '/path/to/privateKey', // 访问令牌的私钥路径。
 *     'passphrase' => null, // 访问令牌的私钥密码。没有密码可以为 `null`。
 * ];
 * // 授权码持续 10 分钟。
 * $authorizationCodeDuration = 600;
 * // 授权码密钥。
 * $authorizationCodeCryptKey => [
 *     'ascii' => 'def0000086937b.....', // 使用 `vendor/bin/generate-defuse-key` 生成的字符串。
 *     //'path' => '/path/to/asciiFile', // 保存了 `vendor/bin/generate-defuse-key` 生成的字符串的文件路径。
 *     //'password' => 'string key', // 字符串密钥。
 * ];
 * // 更新令牌持续 30 天。
 * $refreshTokenDuration = 2592000;
 * // 更新令牌密钥。
 * $refreshTokenCryptKey => [
 *     'ascii' => 'def0000086937b.....', // 使用 `vendor/bin/generate-defuse-key` 生成的字符串。
 *     //'path' => '/path/to/asciiFile', // 保存了 `vendor/bin/generate-defuse-key` 生成的字符串的文件路径。
 *     //'password' => 'string key', // 字符串密钥。
 * ];
 * 
 * 
 * // 授权码模式。
 * // response_type=code
 * $authorizationServer = new AuthorizationServer([
 *     'authorizationCodeRepository' => new AuthorizationCodeRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'defaultScopes' => $defaultScopes,
 *     'authorizationCodeDuration' => $authorizationCodeDuration,
 *     'authorizationCodeCryptKey' => $authorizationCodeCryptKey,
 * ]);
 * // 添加授权类型。
 * $authorizationServer->addAuthorizeType(new CodeAuthorize());
 * // 获取并验证授权请求。
 * $authorizeRequest = $authorizationServer->getAuthorizeRequest($request);
 * // 设置授权的用户。
 * $authorizeRequest->setUser(new UserEntity()); 
 * // 设置同意授权。
 * $authorizeRequest->setIsApproved(true); 
 * // 运行并返回授权成功的回调地址。
 * $redirectUri = $authorizationServer->runAuthorizeTypes($authorizeRequest);
 * 
 * // grant_type=authorization_code
 * $authorizationServer = new AuthorizationServer([
 *     'accessTokenRepository' => new AccessTokenRepository(),
 *     'authorizationCodeRepository' => new AuthorizationCodeRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'refreshTokenRepository' => new RefreshTokenRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'userRepository' => new UserRepository(),
 *     'accessTokenDuration' => $accessTokenDuration,
 *     'accessTokenCryptKey' => $accessTokenCryptKey,
 *     'authorizationCodeCryptKey' => $authorizationCodeCryptKey,
 *     'refreshTokenDuration' => $refreshTokenDuration,
 *     'refreshTokenCryptKey' => $refreshTokenCryptKey,
 * ]);
 * // 添加授予类型。
 * $authorizationServer->addGrantType(new AuthorizationCodeGrant());
 * // 运行并返回授予的认证信息。
 * $credentials = $authorizationServer->runGrantTypes($request);
 * 
 * 
 * // 简单模式。
 * // response_type=token
 * $authorizationServer = new AuthorizationServer([
 *     'accessTokenRepository' => new AccessTokenRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'defaultScopes' => $defaultScopes,
 *     'accessTokenDuration' => $accessTokenDuration,
 *     'accessTokenCryptKey' => $accessTokenCryptKey,
 * ]);
 * // 添加授权类型。
 * $authorizationServer->addAuthorizeType(new ImplicitAuthorize());
 * // 获取并验证授权请求。
 * $authorizeRequest = $authorizationServer->getAuthorizeRequest($request);
 * // 设置授权的用户。
 * $authorizeRequest->setUser(new UserEntity()); 
 * // 设置同意授权。
 * $authorizeRequest->setIsApproved(true); 
 * // 运行并返回授予的认证信息。
 * $credentials = $authorizationServer->runAuthorizeTypes($authorizeRequest);
 * 
 * 
 * // 密码模式。
 * // grant_type=password
 * $authorizationServer = new AuthorizationServer([
 *     'accessTokenRepository' => new AccessTokenRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'refreshTokenRepository' => new RefreshTokenRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'userRepository' => new UserRepository(),
 *     'defaultScopes' => $defaultScopes,
 *     'accessTokenDuration' => $accessTokenDuration,
 *     'accessTokenCryptKey' => $accessTokenCryptKey,
 *     'refreshTokenDuration' => $refreshTokenDuration,
 *     'refreshTokenCryptKey' => $refreshTokenCryptKey,
 * ]);
 * // 添加授予类型。
 * $authorizationServer->addGrantType(new PasswordGrant());
 * // 运行并返回授予的认证信息。
 * $credentials = $authorizationServer->runGrantTypes($request);
 * 
 * 
 * // 客户端模式。
 * // grant_type=client_credentials
 * $authorizationServer = new AuthorizationServer([
 *     'accessTokenRepository' => new AccessTokenRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'defaultScopes' => $defaultScopes,
 *     'accessTokenDuration' => $accessTokenDuration,
 *     'accessTokenCryptKey' => $accessTokenCryptKey,
 * ]);
 * // 添加授予类型。
 * $authorizationServer->addGrantType(new ClientCredentialsGrant());
 * // 运行并返回授予的认证信息。
 * $credentials = $authorizationServer->runGrantTypes($request);
 * 
 * 
 * // 更新令牌。
 * // grant_type=refresh_token
 * $authorizationServer = new AuthorizationServer([
 *     'accessTokenRepository' => new AccessTokenRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'refreshTokenRepository' => new RefreshTokenRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'userRepository' => new UserRepository(),
 *     'accessTokenDuration' => $accessTokenDuration,
 *     'accessTokenCryptKey' => $accessTokenCryptKey,
 *     'refreshTokenDuration' => $refreshTokenDuration,
 *     'refreshTokenCryptKey' => $refreshTokenCryptKey,
 * ]);
 * // 添加授予类型。
 * $authorizationServer->addGrantType(new RefreshTokenGrant());
 * // 运行并返回授予的认证信息。
 * $credentials = $authorizationServer->runGrantTypes($request);
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationServer extends BaseObject
{
    use AuthorizeGrantPropertyTrait, RepositoryPropertyTrait;
    
    /**
     * @var AuthorizeTypeInterface[]
     */
    private $_authorizeTypes = [];
    
    /**
     * @var GrantTypeInterface[]
     */
    private $_grantTypes = [];

    /**
     * 获取授权类型。
     *
     * @return AuthorizeTypeInterface[] 授权类型实例列表。
     */
    protected function getAuthorizeTypes()
    {
        return $this->_authorizeTypes;
    }

    /**
     * 添加授权类型。
     *
     * @param AuthorizeTypeInterface $authorizeType 授权类型。
     */
    public function addAuthorizeType(AuthorizeTypeInterface $authorizeType)
    {
        if ($authorizeType instanceof CodeAuthorize) {
            $this->configureAuthorizeGrantType($authorizeType, [
                'authorizationCodeRepository',
                'clientRepository',
                'scopeRepository',
                'defaultScopes',
                'authorizationCodeDuration',
                'authorizationCodeCryptKey',
            ]);
        } elseif ($authorizeType instanceof ImplicitAuthorize) {
            $this->configureAuthorizeGrantType($authorizeType, [
                'accessTokenRepository',
                'clientRepository',
                'scopeRepository',
                'defaultScopes',
                'accessTokenDuration',
                'accessTokenCryptKey',
            ]);
        }
        
        $this->_grantTypes[$authorizeType->getIdentifier()] = $authorizeType;
    }
    
    /**
     * 获取权限授予类型。
     * 
     * @return GrantTypeInterface[] 权限授予类型实例列表。
     */
    protected function getGrantTypes()
    {
        return $this->_grantTypes;
    }
    
    /**
     * 添加权限授予类型。
     * 
     * @param GrantTypeInterface $grantType 权限授予类型。
     */
    public function addGrantType(GrantTypeInterface $grantType)
    {
        if ($grantType instanceof AuthorizationCodeGrant) {
            $this->configureAuthorizeGrantType($grantType, [
                'accessTokenRepository',
                'authorizationCodeRepository',
                'clientRepository',
                'refreshTokenRepository',
                'scopeRepository',
                'userRepository',
                'accessTokenDuration',
                'accessTokenCryptKey',
                'authorizationCodeCryptKey',
                'refreshTokenDuration',
                'refreshTokenCryptKey',
            ]);
        } elseif ($grantType instanceof ClientCredentialsGrant) {
            $this->configureAuthorizeGrantType($grantType, [
                'accessTokenRepository',
                'clientRepository',
                'scopeRepository',
                'defaultScopes',
                'accessTokenDuration',
                'accessTokenCryptKey',
            ]);
        } elseif ($grantType instanceof PasswordGrant) {
            $this->configureAuthorizeGrantType($grantType, [
                'accessTokenRepository',
                'clientRepository',
                'refreshTokenRepository',
                'scopeRepository',
                'userRepository',
                'defaultScopes',
                'accessTokenDuration',
                'accessTokenCryptKey',
                'refreshTokenDuration',
                'refreshTokenCryptKey',
            ]);
        } elseif ($grantType instanceof RefreshTokenGrant) {
            $this->configureAuthorizeGrantType($grantType, [
                'accessTokenRepository',
                'clientRepository',
                'refreshTokenRepository',
                'scopeRepository',
                'userRepository',
                'accessTokenDuration',
                'accessTokenCryptKey',
                'refreshTokenDuration',
                'refreshTokenCryptKey',
            ]);
        }
        
        $this->_grantTypes[$grantType->getIdentifier()] = $grantType;
    }
    
    /**
     * 配置授权，授予类型实例。
     * 
     * 判断类型实例中的属性是否为 `null`，
     * 如果为 `null`，则使用全局配置，
     * 如果不为 `null`，则不配置属性。
     * 
     * @param AuthorizeTypeInterface|GrantTypeInterface $type 授权，授予类型实例。
     * @param array $names 属性名称列表。
     * @return AuthorizeTypeInterface|GrantTypeInterface
     */
    protected function configureAuthorizeGrantType($type, $names)
    {
        foreach ($names as $name) {
            if ($type->{$name} === null && $this->{$name} !== null) {
                $type->{$name} = $this->{$name};
            }
        }
        
        return $type;
    }
    
    /**
     * 获取授权请求。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return AuthorizeRequestInterface 授权请求。
     * @throws UnsupportedAuthTypeException 不支持的授权类型。
     */
    public function getAuthorizeRequest($request)
    {
        foreach ($this->getAuthorizeTypes() as $identifier => $authorizeType) {
            if ($authorizeType->canRun($request)) {
                return $authorizeType->getAuthorizeRequest($request);
            }
        }
        
        throw new UnsupportedAuthTypeException('The authorization type is not supported by the authorization server.');
    }
    
    /**
     * 运行授权。
     * 
     * @param AuthorizeRequestInterface $authorizeRequest 授权请求。
     * @return string 回调地址。
     * @throws UserDeniedAuthorizeException 用户拒绝授权。
     */
    public function runAuthorizeTypes(AuthorizeRequestInterface $authorizeRequest)
    {
        return $authorizeRequest->getAuthorizeType()->run($authorizeRequest);
    }
    
    /**
     * 运行权限授予。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return array 认证信息。
     * @throws UnsupportedAuthTypeException 不支持的授予类型。
     */
    public function runGrantTypes($request)
    {
        foreach ($this->getGrantTypes() as $identifier => $grantType) {
            if ($grantType->canRun($request)) {
                return $grantType->run($request);
            }
        }
        
        throw new UnsupportedAuthTypeException('The grant type is not supported by the authorization server.');
    }
}