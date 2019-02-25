<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\validators;

/**
 * MobileValidator 验证手机号码格式是否有效。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class MobileValidator extends \yii\validators\RegularExpressionValidator
{
    /**
     * @var string // 正则表达式。以 1 开头的 11 位数字。
     */
    public $pattern = '/^1\d{10}$/';
}