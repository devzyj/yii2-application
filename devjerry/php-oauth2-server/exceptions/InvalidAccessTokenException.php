<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\exceptions;

/**
 * InvalidAccessTokenException 表示无效的访问令牌，状态码为 401 的 HTTP 异常。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class InvalidAccessTokenException extends OAuthServerException
{
    /**
     * Constructor.
     * 
     * @param string $message 错误信息。
     * @param int $code 错误编码。
     * @param \Exception $previous 前一个异常。
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct(401, $message, $code, $previous);
    }
}
