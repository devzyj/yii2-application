<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\exceptions;

/**
 * OAuthServerException 授权服务器异常类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OAuthServerException extends \Exception
{
    /**
     * @var integer HTTP status code, such as 403, 404, 500, etc.
     */
    private $_httpStatusCode;
    
    /**
     * @var string 回调地址。
     */
    private $_redirectUri;

    /**
     * Constructor.
     * 
     * @param integer $status HTTP 状态码。
     * @param string $message 错误信息。
     * @param integer $code 错误编码。
     * @param \Exception $previous 上一个异常。
     */
    public function __construct($status, $message, $code = 0, \Exception $previous = null)
    {
        $this->_httpStatusCode = $status;
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * 获取 HTTP 状态码。
     * 
     * @return integer
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }
    
    /**
     * 获取回调地址。
     * 
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->_redirectUri;
    }
    
    /**
     * 设置回调地址。
     * 
     * @param string $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->_redirectUri = $redirectUri;
    }
    
    /**
     * 获取需要添加到响应头的内容。
     * 
     * @return array
     */
    public function getResponseHeaders()
    {
        return [];
    }
}
