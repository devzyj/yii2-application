<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

/**
 * ArrayHelper class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ArrayHelper
{
    /**
     * 获取数组中指定的值。
     * 
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($array, $key, $default = null)
    {
        if (!is_array($array) || empty($array) || !isset($array[$key]) || !array_key_exists($key, $array)) {
            return $default;
        }
        
        return $array[$key];
    }
}