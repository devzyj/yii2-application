<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components\filters;

/**
 * ClientStatusFilterInterface 是可以由标识对象实现的接口，用于验证客户端状态是否有效。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface ClientStatusFilterInterface
{
    /**
     * 验证客户端状态是否有效。
     * 
     * @param \yii\base\Action $action 将要执行的动作。
     * @param \yii\web\Request $request 当前请求对像。
     * @return boolean 是否有效。
     */
    public function checkClientStatus($action, $request);
}
