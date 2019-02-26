<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\validators;

/**
 * UsernameValidator 验证用户名格式是否有效。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UsernameValidator extends \yii\validators\RegularExpressionValidator
{
    /**
     * @var string // 正则表达式。支持 3-20 位的英文、数字和下划线，且必需以英文开头。
     */
    public $pattern = '/^[A-Za-z][\_A-Za-z0-9]{2,19}$/u';
}