<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

/**
 * 授权类型接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface GrantTypeInterface
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
     * 运行授权。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return mixed
     */
    public function run(ServerRequestInterface $request);

}