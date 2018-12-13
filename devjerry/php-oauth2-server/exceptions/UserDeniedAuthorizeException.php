<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\exceptions;

/**
 * UserDeniedAuthorizeException 用户拒绝授权的异常。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UserDeniedAuthorizeException extends OAuthServerException
{
    /**
     * @var string 回调地址。
     */
    public $redirectUri;
    
    /**
     * Constructor.
     * 
     * @param array $redirectUri 回调地址。
     * @param string $message 错误信息。
     * @param int $code 错误编码。
     * @param \Exception $previous 前一个异常。
     */
    public function __construct($redirectUri, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->redirectUri = $redirectUri;
        
        if ($message === null) {
            $message = 'The user denied the authorization.';
        }
        
        parent::__construct(401, $message, $code, $previous);
    }
}
