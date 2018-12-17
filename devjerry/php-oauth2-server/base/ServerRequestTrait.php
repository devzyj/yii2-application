<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;

/**
 * ServerRequestTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait ServerRequestTrait
{
    /**
     * 获取请求的查询字符串参数。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @param string $name 参数名称。
     * @param mixed $default 默认值。
     * @return null|string
     */
    protected function getRequestQueryParam($request, $name, $default = null)
    {
        $params = (array) $request->getQueryParams();
        return isset($params[$name]) ? $params[$name] : $default;
    }
    
    /**
     * 获取请求的实体参数。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @param string $name 参数名称。
     * @param mixed $default 默认值。
     * @return null|string
     */
    protected function getRequestBodyParam($request, $name, $default = null)
    {
        $params = (array) $request->getParsedBody();
        return isset($params[$name]) ? $params[$name] : $default;
    }

    /**
     * 获取服务器参数。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @param string $name 参数名称。
     * @param mixed $default 默认值。
     * @return null|string
     */
    protected function getRequestServerParam($request, $name, $default = null)
    {
        $params = (array) $request->getServerParams();
        return isset($params[$name]) ? $params[$name] : $default;
    }

    /**
     * 获取请求头参数。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @param string $name 参数名称。
     * @param mixed $default 默认值。
     * @param boolean $first 是否返回第一个元素。
     * @return null|string|array
     */
    protected function getRequestHeaderParam($request, $name, $default = null, $first = true)
    {
        $params = (array) $request->getHeaders();
        $name = strtolower($name);
        if (isset($params[$name])) {
            return $first ? reset($params[$name]) : $params[$name];
        }
        
        return $default;
    }
    
    /**
     * 在请求的授权头中检索 HTTP 基本身份验证凭据。
     * 返回数组的第一个索引是用户名，第二个是密码。
     * 如果报头不存在，或者是无效的 HTTP 基本报头，则返回 [null, null]。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return string[]|null[]
     */
    protected function getRequestAuthCredentials($request)
    {
        $username = $this->getRequestServerParam($request, 'PHP_AUTH_USER');
        $password = $this->getRequestServerParam($request, 'PHP_AUTH_PW');
        if ($username !== null || $password !== null) {
            return [$username, $password];
        }
        
        $authorization = $this->getRequestHeaderParam($request, 'Authorization');
        if ($authorization !== null && strncasecmp($authorization, 'basic', 5) === 0) {
            $parts = array_map(function ($value) {
                return strlen($value) === 0 ? null : $value;
            }, explode(':', base64_decode(mb_substr($authorization, 6)), 2));
        
            if (count($parts) < 2) {
                return [$parts[0], null];
            }
    
            return $parts;
        }
        
        return [null, null];
    }

    /**
     * 在请求的授权头中检索 HTTP 基本身份验证凭据的用户名。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return string|null
     */
    protected function getRequestAuthUser($request)
    {
        return $this->getRequestAuthCredentials($request)[0];
    }

    /**
     * 在请求的授权头中检索 HTTP 基本身份验证凭据的用户密码。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return string|null
     */
    protected function getRequestAuthPassword($request)
    {
        return $this->getRequestAuthCredentials($request)[1];
    }

    /**
     * 在请求的授权头中检索 HTTP authentication。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return string|null
     */
    protected function getRequestAuthorization($request)
    {
        $authorization = $this->getRequestHeaderParam($request, 'authorization');
        if ($authorization !== null && preg_match('/^Bearer\s+(.*?)$/', $authorization, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}