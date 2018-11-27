<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\validators\backend;

/**
 * PasswordValidator 验证密码格式是否有效。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class PasswordValidator extends \yii\validators\RegularExpressionValidator
{
    /**
     * @var string // 正则表达式。支持 6-20 位的任意字符。
     */
    public $pattern = '/^[\s\S]{6,20}$/u';
}