<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\grants;

use devjerry\oauth2\server\interfaces\ServerRequestInterface;

/**
 * 权限授予类型接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface GrantTypeInterface
{
    /**
     * 获取授予标识。
     * 
     * @return string 授予标识。
     */
    public function getIdentifier();

    /**
     * 是否可以运行权限授予。
     *
     * @param ServerRequestInterface $request 服务器请求。
     * @return boolean
     */
    public function canRun($request);
    
    /**
     * 运行权限授予。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return array 认证信息。
     */
    public function run($request);
}