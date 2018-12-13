<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\AuthorizationCodeEntityInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\exceptions\OAuthServerException;

/**
 * AuthorizationCodeGrant class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationCodeGrant extends AbstractGrant
{
    /**
     * @var boolean
     */
    protected $enableCodeChallenge = false;

    /**
     * 启用代码交换验证。
     */
    public function enableCodeChallenge()
    {
        $this->enableCodeChallenge = true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::GRANT_TYPE_AUTHORIZATION_CODE;
    }

    /**
     * {@inheritdoc}
     */
    protected function runGrant(ServerRequestInterface $request, ClientEntityInterface $client)
    {
        // 获取请求的授权码。
        $authorizationCode = $this->getRequestedAuthorizationCode($request);

        // 获取回调地址。
        $redirectUri = $this->getRequestBodyParam($request, 'redirect_uri');
        if ($redirectUri === null) {
            throw new OAuthServerException(400, 'Missing parameters: "redirect_uri" required.');
        }
        
        // 验证请求的授权码。
        $this->validateAuthorizationCode($authorizationCode, $client, $redirectUri);
        
        // 验证交换代码。
        $this->validateCodeChallenge($request, $authorizationCode);

        // 获取与授权码关联的用户。
        $user = $this->getUserRepository()->getUserEntity($authorizationCode->getUserIdentifier());
        if (!$user instanceof UserEntityInterface) {
            throw new OAuthServerException(400, 'Invalid user.');
        }
        
        // 验证与授权码关联的权限。
        $authorizationCodeScopes = $this->validateScopes($authorizationCode->getScopeIdentifiers());

        // 确定最终授予的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalizeEntities($authorizationCodeScopes, $this->getIdentifier(), $client, $user);

        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($finalizedScopes, $client, $user);
        
        // 创建更新令牌。
        $refreshToken = $this->generateRefreshToken($accessToken);
        
        // 生成认证信息。
        $credentials = $this->generateCredentials($accessToken, $refreshToken);

        // 撤销授权码。
        $this->getAuthorizationCodeRepository()->revokeAuthorizationCodeEntity($authorizationCode->getIdentifier());

        // 返回认证信息。
        return $credentials;
    }

    /**
     * 获取请求的授权码。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return AuthorizationCodeEntityInterface 授权码。
     * @throws OAuthServerException 缺少参数。
     */
    protected function getRequestedAuthorizationCode(ServerRequestInterface $request)
    {
        $requestedCode = $this->getRequestBodyParam($request, 'code');
        if ($requestedCode === null) {
            throw new OAuthServerException(400, 'Missing parameters: "code" required.');
        }
        
        $authorizationCode = $this->getAuthorizationCodeRepository()->unserializeAuthorizationCodeEntity($requestedCode, $this->getAuthorizationCodeCryptKey());
        if (!$authorizationCode instanceof AuthorizationCodeEntityInterface) {
            throw new OAuthServerException(401, 'Authorization code is invalid.');
        }
        
        return $authorizationCode;
    }
    
    /**
     * 验证请求的授权码。
     * 
     * @param AuthorizationCodeEntityInterface $authorizationCode 授权码。
     * @param ClientEntityInterface $client 客户端。
     * @param string $redirectUri 回调地址。
     * @throws OAuthServerException 授权码没有关联到当前客户端，或者授权码过期，或者回调地址错误，或者授权码已撤销。
     */
    protected function validateRefreshToken(AuthorizationCodeEntityInterface $authorizationCode, ClientEntityInterface $client, $redirectUri)
    {
        if ($authorizationCode->getClientIdentifier() != $client->getIdentifier()) {
            throw new OAuthServerException(401, 'Authorization code was not issued to this client.');
        } elseif ($authorizationCode->getExpires() < time()) {
            throw new OAuthServerException(401, 'Authorization code has expired.');
        } elseif ($authorizationCode->getRedirectUri() !== $redirectUri) {
            throw new OAuthServerException(401, 'Invalid redirect URI.');
        } elseif ($this->getAuthorizationCodeRepository()->isAuthorizationCodeEntityRevoked($authorizationCode->getIdentifier())) {
            throw new OAuthServerException(401, 'Authorization code has been revoked.');
        }
    }
    
    /**
     * 验证交换代码。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @param AuthorizationCodeEntityInterface $authorizationCode 授权码。
     * @throws OAuthServerException 验证错误。
     */
    protected function validateCodeChallenge(ServerRequestInterface $request, $authorizationCode)
    {
        if ($this->enableCodeChallenge === true) {
            $codeVerifier = $this->getRequestBodyParam('code_verifier', $request);
            if ($codeVerifier === null) {
                throw new OAuthServerException(400, 'Missing parameters: "code_verifier" required.');
            }
            
            // Validate code_verifier according to RFC-7636
            // @see: https://tools.ietf.org/html/rfc7636#section-4.1
            if (preg_match('/^[A-Za-z0-9-._~]{43,128}$/', $codeVerifier) !== 1) {
                throw new OAuthServerException(400, 'Code Verifier must follow the specifications of RFC-7636.');
            }

            $codeChallenge = $authorizationCode->getCodeChallenge();
            $codeChallengeMethod = $authorizationCode->getCodeChallengeMethod();
            switch ($codeChallengeMethod) {
                case 'plain':
                    if (hash_equals($codeVerifier, $codeChallenge) === false) {
                        throw new OAuthServerException(400, 'Failed to verify `code_verifier`.');
                    }
                    break;
                case 'S256':
                    $codeVerifier = strtr(rtrim(base64_encode(hash('sha256', $codeVerifier, true)), '='), '+/', '-_');
                    if (hash_equals($codeVerifier, $codeChallenge) === false) {
                        throw new OAuthServerException(400, 'Failed to verify `code_verifier`.');
                    }
                    break;
                default:
                    throw new OAuthServerException(500, sprintf('Unsupported code challenge method `%s`', $codeChallengeMethod));
            }
        }
    }
}

// PHP < 5.6
if (!function_exists('hash_equals')) {
    function hash_equals($a, $b)
    {
        return substr_count($a ^ $b, "\0") * 2 === strlen($a . $b);
    }
}