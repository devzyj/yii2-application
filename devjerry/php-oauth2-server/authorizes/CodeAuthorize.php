<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

use devjerry\oauth2\server\exceptions\OAuthServerException;
use devjerry\oauth2\server\exceptions\BadRequestException;

/**
 * CodeAuthorize class.
 * 
 * ```php
 * use devjerry\oauth2\server\authorizes\CodeAuthorize;
 * 
 * // 实例化对像。
 * $codeAuthorize = new CodeAuthorize([
 *     'authorizationCodeRepository' => new AuthorizationCodeRepository(),
 *     'clientRepository' => new ClientRepository(),
 *     'scopeRepository' => new ScopeRepository(),
 *     'defaultScopes' => ['basic', 'basic2'], // 默认权限。
 *     'authorizationCodeDuration' => 600, // 授权码持续 10 分钟。
 *     'authorizationCodeCryptKey' => [
 *         'ascii' => 'def0000086937b.....', // 使用 `vendor/bin/generate-defuse-key` 生成的字符串。
 *         //'path' => '/path/to/asciiFile', // 保存了 `vendor/bin/generate-defuse-key` 生成的字符串的文件路径。
 *         //'password' => 'string key', // 字符串密钥。
 *     ]
 *     //'enableCodeChallenge' => true,
 *     //'defaultCodeChallengeMethod' => 'plain',
 * ]);
 * ```
 * 
 * @property boolean $enableCodeChallenge 是否启用代码交换验证。
 * @property string $defaultCodeChallengeMethod 代码交换验证方法。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CodeAuthorize extends AbstractAuthorize
{
    /**
     * @var boolean
     */
    private $_enableCodeChallenge;

    /**
     * @var string
     */
    private $_defaultCodeChallengeMethod;
    
    /**
     * 获取是否启用代码交换验证。
     * 
     * @return boolean
     */
    public function getEnableCodeChallenge()
    {
        return $this->_enableCodeChallenge;
    }
    
    /**
     * 设置是否启用代码交换验证。
     * 
     * @param boolean $value
     */
    public function setEnableCodeChallenge($value)
    {
        $this->_enableCodeChallenge = (bool) $value;
    }

    /**
     * 获取代码交换验证方法。
     *
     * @return string
     */
    public function getDefaultCodeChallengeMethod()
    {
        return $this->_defaultCodeChallengeMethod;
    }
    
    /**
     * 设置代码交换验证方法。
     *
     * @param string $value
     */
    public function setDefaultCodeChallengeMethod($value)
    {
        $this->_defaultCodeChallengeMethod = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if ($this->getEnableCodeChallenge() === null) {
            $this->setEnableCodeChallenge(false);
        }
        
        if ($this->getDefaultCodeChallengeMethod() === null) {
            $this->setDefaultCodeChallengeMethod('plain');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getIdentifier()
    {
        return self::AUTHORIZE_TYPE_CODE;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGrantIdentifier()
    {
        return self::GRANT_TYPE_AUTHORIZATION_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function canRun($request)
    {
        if ($this->getAuthorizationCodeRepository() === null) {
            throw new \LogicException('The `authorizationCodeRepository` property must be set.');
        }
        
        return parent::canRun($request);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @throws BadRequestException 缺少参数，或者参数无效。
     */
    public function getAuthorizeRequest($request)
    {
        $authorizeRequest = parent::getAuthorizeRequest($request);

        try {
            // 启用交换码的验证。
            if ($this->getEnableCodeChallenge() === true) {
                $codeChallenge = $this->getRequestQueryParam($request, 'code_challenge');
                if ($codeChallenge === null) {
                    throw new BadRequestException('Missing parameters: `code_challenge` required.');
                }
                
                $codeChallengeMethod = $this->getRequestQueryParam($request, 'code_challenge_method', $this->getDefaultCodeChallengeMethod());
                if (!in_array($codeChallengeMethod, ['plain', 'S256'], true)) {
                    throw new BadRequestException('Code challenge method must be `plain` or `S256`.');
                }
                
                // Validate code_challenge according to RFC-7636
                // @see: https://tools.ietf.org/html/rfc7636#section-4.2
                if (preg_match('/^[A-Za-z0-9-._~]{43,128}$/', $codeChallenge) !== 1) {
                    throw new BadRequestException('Code challenge must follow the specifications of RFC-7636.');
                }
    
                $authorizeRequest->setCodeChallenge($codeChallenge);
                $authorizeRequest->setCodeChallengeMethod($codeChallengeMethod);
            }
        } catch (OAuthServerException $exception) {
            $redirectUri = $authorizeRequest->getRedirectUri();
            $state = $authorizeRequest->getState();
            
            // 设置异常的回调地址。
            $exception->setRedirectUri($this->makeRedirectUri($redirectUri, ['state' => $state]));
            throw $exception;
        }
        
        return $authorizeRequest;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function runUserAllowed(AuthorizeRequestInterface $authorizeRequest)
    {
        $authorizationCode = $this->generateAuthorizationCode($authorizeRequest);
        $authorizationCryptKey = $this->getAuthorizationCodeCryptKey();
        
        $authorizationCodeRepository = $this->getAuthorizationCodeRepository();
        $code = $authorizationCodeRepository->serializeAuthorizationCodeEntity($authorizationCode, $authorizationCryptKey);
        
        // 返回授权成功的回调地址。
        return $this->makeRedirectUri($authorizeRequest->getRedirectUri(), [
            'code' => $code,
            'state' => $authorizeRequest->getState(),
        ]);
    }
}