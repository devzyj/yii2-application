<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

/**
 * FunctionHelper class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class FunctionHelper
{
    /**
     * 可防止时序攻击的字符串比较。
     * 
     * @param string $known_string 已知长度的、要参与比较的字符串。
     * @param string $user_string 用户提供的字符串。
     * @return boolean 当两个字符串相等时返回 `TRUE`，否则返回 `FALSE`。
     */
    public static function hashEquals($known_string, $user_string)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($known_string, $user_string);
        }
        
        // PHP < 5.6
        return substr_count($known_string ^ $user_string, "\0") * 2 === strlen($known_string . $user_string);
    }
}