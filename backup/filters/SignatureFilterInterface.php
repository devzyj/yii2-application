<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backup\filters;

/**
 * SignatureFilterInterface 由登录用户标识对像实现，用于验证签名。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 * @deprecated
 */
interface SignatureFilterInterface
{
    /**
     * 返回当前请求真实有效的签名。
     * 
     * @param \yii\web\Request $request 当前请求对像。
     * @param \yii\base\Action $action 将要执行的动作。
     * @return string 有效的签名。
     */
    public function getRealSignature($request, $action);
}
