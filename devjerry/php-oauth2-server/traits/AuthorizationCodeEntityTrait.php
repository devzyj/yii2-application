<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\traits;

/**
 * AuthorizationCodeEntityTrait 实现了 [[AuthorizationCodeEntityInterface]] 中的方法。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait AuthorizationCodeEntityTrait
{
    use BaseTokenCodeEntityTrait;
    
    /**
     * @var string
     */
    private $_redirectUri;
    
    /**
     * @var string
     */
    private $_codeChallenge;

    /**
     * @var string
     */
    private $_codeChallengeMethod;

    /**
     * 获取授权码的回调地址。
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->_redirectUri;
    }

    /**
     * 设置授权码的回调地址。
     *
     * @param string $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->_redirectUri = $redirectUri;
    }

    /**
     * 获取交换验证代码。
     *
     * @return string
     */
    public function getCodeChallenge()
    {
        return $this->_codeChallenge;
    }

    /**
     * 设置交换验证代码。
     *
     * @param string $codeChallenge
     */
    public function setCodeChallenge($codeChallenge)
    {
        $this->_codeChallenge = $codeChallenge;
    }

    /**
     * 获取交换验证方法。
     *
     * @return string
     */
    public function getCodeChallengeMethod()
    {
        return $this->_codeChallengeMethod;
    }

    /**
     * 设置交换验证方法。
     *
     * @param string $codeChallengeMethod
     */
    public function setCodeChallengeMethod($codeChallengeMethod)
    {
        $this->_codeChallengeMethod = $codeChallengeMethod;
    }
}