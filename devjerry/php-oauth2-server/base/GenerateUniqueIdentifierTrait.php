<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

use devjerry\oauth2\server\exceptions\OAuthServerException;

/**
 * GenerateUniqueIdentifierTrait 提供了生成唯一标识的方法。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait GenerateUniqueIdentifierTrait
{
    /**
     * 生成唯一标识。
     * 
     * @param int $length 长度。
     * @return string 唯一标识。
     * @throws OAuthServerException 生成失败。
     */
    protected function generateUniqueIdentifier($length = 40)
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (\TypeError $e) {
            throw new OAuthServerException(500, 'An unexpected error has occurred.');
        } catch (\Error $e) {
            throw new OAuthServerException(500, 'An unexpected error has occurred.');
        } catch (\Exception $e) {
            throw new OAuthServerException(500, 'Could not generate a random string.');
        }
    }
}
