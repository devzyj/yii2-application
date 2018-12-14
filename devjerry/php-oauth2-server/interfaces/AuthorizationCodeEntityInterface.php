<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

/**
 * 授权码实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AuthorizationCodeEntityInterface extends BaseTokenCodeEntityInterface
{
    /**
     * 获取授权码的回调地址。
     *
     * @return string
     */
    public function getRedirectUri();
    
    /**
     * 设置授权码的回调地址。
     *
     * @param string $redirectUri
     */
    public function setRedirectUri($redirectUri);

    /**
     * 获取交换验证代码。
     *
     * @return string
     */
    public function getCodeChallenge();
    
    /**
     * 设置交换验证代码。
     *
     * @param string $codeChallenge
     */
    public function setCodeChallenge($codeChallenge);

    /**
     * 获取交换验证方法。
     *
     * @return string
     */
    public function getCodeChallengeMethod();
    
    /**
     * 设置交换验证方法。
     *
     * @param string $codeChallengeMethod
     */
    public function setCodeChallengeMethod($codeChallengeMethod);
}