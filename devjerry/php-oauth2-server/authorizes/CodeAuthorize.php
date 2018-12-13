<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\exceptions\OAuthServerException;

/**
 * CodeAuthorize class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CodeAuthorize extends AbstractAuthorize
{
    /**
     * @var boolean
     */
    protected $enableCodeChallenge = false;
    
    /**
     * @var string 代码交换验证方法。
     */
    protected $defaultCodeChallengeMethod = 'plain';

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
        return self::AUTHORIZE_TYPE_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function getGrantIdentifier()
    {
        return self::GRANT_TYPE_AUTHORIZATION_CODE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAuthorizeRequest(ServerRequestInterface $request)
    {
        $authorizeRequest = parent::getAuthorizeRequest($request);

        // 启用交换码的验证。
        if ($this->enableCodeChallenge === true) {
            $codeChallenge = $this->getRequestQueryParam($request, 'code_challenge');
            if ($codeChallenge === null) {
                throw new OAuthServerException(400, 'Missing parameters: "code_challenge" required.');
            }
            
            $codeChallengeMethod = $this->getRequestQueryParam($request, 'code_challenge_method', $this->defaultCodeChallengeMethod);
            if (!in_array($codeChallengeMethod, ['plain', 'S256'], true)) {
                throw new OAuthServerException(400, 'Code challenge method must be `plain` or `S256`.');
            }
            
            // Validate code_challenge according to RFC-7636
            // @see: https://tools.ietf.org/html/rfc7636#section-4.2
            if (preg_match('/^[A-Za-z0-9-._~]{43,128}$/', $codeChallenge) !== 1) {
                throw new OAuthServerException(400, 'Code challenge must follow the specifications of RFC-7636.');
            }

            $authorizeRequest->setCodeChallenge($codeChallenge);
            $authorizeRequest->setCodeChallengeMethod($codeChallengeMethod);
        }
        
        return $authorizeRequest;
    }
    
    /**
     * {@inheritdoc}
     */
    public function runUserAllowed(AuthorizeRequestInterface $authorizeRequest)
    {
        $authorizationCodeRepository = $this->getAuthorizationCodeRepository();
        $authorizationCode = $this->generateAuthorizationCode($authorizeRequest);
        $authorizationCryptKey = $this->getAuthorizationCodeCryptKey();
        $code = $authorizationCodeRepository->serializeAuthorizationCodeEntity($authorizationCode, $authorizationCryptKey);
        
        // 返回授权成功的回调地址。
        return $this->makeRedirectUri($authorizeRequest->getRedirectUri(), [
            'code' => $code,
            'state' => $authorizeRequest->getState(),
        ]);
    }
}