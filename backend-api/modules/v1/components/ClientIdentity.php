<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiV1\components;

use backendApi\filters\ClientIpFilterInterface;

/**
 * 访问接口的客户端标识类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientIdentity extends \backendApi\components\ClientIdentity implements ClientIpFilterInterface
{
    /******************************* ClientIpFilterInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function checkAllowedClientIp($ip, $action, $request)
    {
        return $this->checkAllowedIp($ip);
    }
}
