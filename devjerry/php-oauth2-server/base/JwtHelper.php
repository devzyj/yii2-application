<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Token;

/**
 * JwtHelper
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class JwtHelper
{
    const SIGN_ALG_HS256 = 'HS256';
    const SIGN_ALG_HS384 = 'HS384';
    const SIGN_ALG_HS512 = 'HS512';
    const SIGN_ALG_RS256 = 'RS256';
    const SIGN_ALG_RS384 = 'RS384';
    const SIGN_ALG_RS512 = 'RS512';
    
    /**
     * @var array 签名类型。
     */
    protected static $algs = [
        'HS256' => 'Lcobucci\JWT\Signer\Hmac\Sha256',
        'HS384' => 'Lcobucci\JWT\Signer\Hmac\Sha384',
        'HS512' => 'Lcobucci\JWT\Signer\Hmac\Sha512',
        'RS256' => 'Lcobucci\JWT\Signer\Rsa\Sha256',
        'RS384' => 'Lcobucci\JWT\Signer\Rsa\Sha384',
        'RS512' => 'Lcobucci\JWT\Signer\Rsa\Sha512',
    ];
    
    /**
     * 获取签名算法。
     * 
     * @param string $alg
     * @return string|null
     */
    protected static function getAlg($alg)
    {
        return isset(self::$algs[$alg]) ? self::$algs[$alg] : null;
    }
    
    /**
     * 创建生成器。
     * 
     * @return Builder
     */
    public static function createBuilder()
    {
        return new Builder();
    }
    
    /**
     * 令牌签名。
     * 
     * @param Builder $builder 生成器。
     * @param string $alg 签名算法。可以是 `HS256`, `HS384`, `HS512`, `RS256`, `RS384`, `RS512`。
     * @param string|array $key 签名密钥。
     *     - `$alg` 等于 `HS256`, `HS384`, `HS512` 时，应该是一个字符串密钥。如果不是字符串，则使用 [[serialize()]] 转换成字符串。
     *     - `$alg` 等于 `RS256`, `RS384`, `RS512` 时，应该是一个包函一个或二个元素的数组，
     *         - 第一个元素为私钥路径。
     *         - 第二个元素为私钥密码，如果没有密码，可以为 `null`。
     * @return Builder
     * @see ensureSignKey()
     * @throws \InvalidArgumentException 无效的参数。
     */
    public static function sign(Builder $builder, $alg, $key)
    {
        $signAlg = self::getAlg($alg);
        if ($signAlg === null) {
            throw new \InvalidArgumentException('The `alg` is invalid.');
        }

        $signKey = self::ensureSignKey($alg, $key);
        return $builder->sign(new $signAlg, $signKey);
    }
    
    /**
     * 确认签名密钥。
     * 
     * @param string $alg 签名算法。可以是 `HS256`, `HS384`, `HS512`, `RS256`, `RS384`, `RS512`。
     * @param string|array $key 签名密钥。
     *     - `$alg` 等于 `HS256`, `HS384`, `HS512` 时，应该是一个字符串密钥。如果不是字符串，则使用 [[serialize()]] 转换成字符串。
     *     - `$alg` 等于 `RS256`, `RS384`, `RS512` 时，应该是一个包函一个或二个元素的数组，
     *         - 第一个元素为私钥、公钥路径。
     *         - 第二个元素为私钥密码，如果没有密码，可以为 `null`。
     * @return Key|string
     * @throws \InvalidArgumentException 无效的参数。
     */
    protected static function ensureSignKey($alg, $key)
    {
        if ($alg === self::SIGN_ALG_HS256 || $alg === self::SIGN_ALG_HS384 || $alg === self::SIGN_ALG_HS512) {
            return is_string($key) ? $key : serialize($key);
        } elseif ($alg === self::SIGN_ALG_RS256 || $alg === self::SIGN_ALG_RS384 || $alg === self::SIGN_ALG_RS512) {
            if (!is_array($key)) {
                throw new \InvalidArgumentException('The `key` must be an array.');
            } elseif (count($key) === 1) {
                $path = reset($key);
                $passphrase = null;
            } else {
                list ($path, $passphrase) = $key;
            }
        
            if (strpos($path, 'file://') !== 0) {
                $path = 'file://' . $path;
            }
        
            return new Key($path, $passphrase);
        }
        
        return $key;
    }

    /**
     * 解析令牌。
     *
     * @param string $jwt 令牌。
     * @return Token 令牌实例。
     * @throws \InvalidArgumentException 解析令牌出错。
     * @throws \RuntimeException 解码JSON时出错。
     */
    public static function parseJwt($jwt)
    {
        return (new Parser())->parse((string) $jwt);
    }
    
    /**
     * 验证令牌签名。
     * 
     * @param Token $token 令牌实例。
     * @param string|array $key 签名密钥。
     *     - `$token->alg` 等于 `HS256`, `HS384`, `HS512` 时，应该是一个字符串密钥。如果不是字符串，则使用 [[serialize()]] 转换成字符串。
     *     - `$token->alg` 等于 `RS256`, `RS384`, `RS512` 时，应该是一个包函一个元素的数组，表示公钥路径。
     * @param boolean $notSigned 令牌没有签名时的返回值。默认为 `false`。
     * @return boolean
     */
    public static function verify(Token $token, $key, $notSigned = false)
    {
        $alg = $token->getHeader('alg');
        $signAlg = self::getAlg($alg);
        if ($signAlg === null) {
            return $notSigned;
        }
        
        $signKey = self::ensureSignKey($alg, $key);
        return $token->verify(new $signAlg, $signKey);
    }
    
    /**
     * 验证令牌是否过期。
     * 
     * @param Token $token 令牌实例。
     * @return boolean
     */
    public static function validateExpires(Token $token)
    {
        $data = new ValidationData();
        $data->setCurrentTime(time());
        
        return $token->validate($data);
    }
}
