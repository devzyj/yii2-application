<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\components\filters;

/**
 * ClientIpFilterInterface 是可以由标识对象实现的接口，用于验证客户端 IP 是否被允许访问。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface ClientIpFilterInterface
{
    /**
     * 验证客户端 IP 是否被允许访问。
     * 
     * @param string $ip 客户端 IP。
     * @param \yii\base\Action $action 将要执行的动作。
     * @param \yii\web\Request $request 当前请求对像。
     * @return boolean 是否允许。
     */
    public function checkClientIp($ip, $action, $request);
}
