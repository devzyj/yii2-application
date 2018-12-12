<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;

/**
 * 授权类型接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AuthorizeTypeInterface
{
    /**
     * 获取授权标识。
     * 
     * @return string 授权标识。
     */
    public function getIdentifier();
    
    /**
     * 是否可以运行授权。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return boolean
     */
    public function canRun(ServerRequestInterface $request);
    
    /**
     * 获取授权请求。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return AuthorizeRequestInterface 授权请求。
     */
    public function getAuthorizeRequest(ServerRequestInterface $request);

    /**
     * 运行授权。
     *
     * @param AuthorizeRequestInterface $authorizeRequest 授权请求。
     * @return array 回调地址信息。
     */
    public function run(AuthorizeRequestInterface $authorizeRequest);

    /**
     * 获取与授权类型有关的授予类型标识。
     *
     * @return string 授予标识。
     */
    public function getGrantIdentifier();
}