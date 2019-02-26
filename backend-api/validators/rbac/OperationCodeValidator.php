<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\validators\rbac;

/**
 * OperationCodeValidator 验证操作编码是否有效。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OperationCodeValidator extends \yii\validators\RegularExpressionValidator
{
    /**
     * @var string 正则表达式（必须是以英文，数字，下划线组成的字符串）。
     */
    public $pattern = '/^[\_A-Za-z0-9]*$/';
}
