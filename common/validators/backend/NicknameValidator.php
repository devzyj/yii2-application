<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\validators\backend;

/**
 * NicknameValidator 验证昵称格式是否有效。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class NicknameValidator extends \yii\validators\RegularExpressionValidator
{
    /**
     * @var string // 正则表达式。支持 2-20 位的中英文、数字和下划线。
     */
    public $pattern = '/^[\_A-Za-z0-9\x{4e00}-\x{9fa5}]{2,20}$/u';
}