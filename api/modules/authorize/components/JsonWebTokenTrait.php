<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiAuthorize\components;

use Yii;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

/**
 * JsonWebTokenTrait 解析并且验证 `token` 的有效性。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait JsonWebTokenTrait
{
    /**
     * @var array 加密类型。
     */
    protected static $jwtAlgs = [
        'HS256' => 'Lcobucci\JWT\Signer\Hmac\Sha256',
        'HS384' => 'Lcobucci\JWT\Signer\Hmac\Sha384',
        'HS512' => 'Lcobucci\JWT\Signer\Hmac\Sha512',
    ];
    
    /**
     * 加载令牌。
     * 
     * @param string $token 令牌。
     * @param string $key 令牌的加密 KEY。
     * @return \Lcobucci\JWT\Token|null 令牌模型。
     */
    public static function loadJwt($token, $key = null)
    {
        $token = static::parseJwt($token);
        if (!$token) {
            return;
        } elseif (!static::verifyJwt($token, $key)) {
            return;
        } elseif (!static::validateJwt($token)) {
            return;
        }
        
        return $token;
    }
    
    /**
     * 解析令牌。
     * 
     * @param string $token 令牌。
     * @return \Lcobucci\JWT\Token 令牌模型，如果无效则为 `null`。
     */
    protected static function parseJwt($token)
    {
        try {
            return Yii::createObject(Parser::class)->parse((string)$token);
        } catch (\RuntimeException $e) {
            return null;
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
    
    /**
     * 验证令牌有效性。
     * 
     * @param \Lcobucci\JWT\Token $token 令牌模型。
     * @param string $key 令牌的加密 KEY。
     * @return boolean
     */
    protected static function verifyJwt($token, $key = null)
    {
        $alg = $token->getHeader('alg');
        if (isset(self::$jwtAlgs[$alg])) {
            $signer = Yii::createObject(self::$jwtAlgs[$alg]);
            return $token->verify($signer, $key);
        }
        
        return true;
    }

    /**
     * 验证令牌数据。
     * 
     * @param \Lcobucci\JWT\Token $token 令牌模型。
     * @return boolean
     */
    protected static function validateJwt($token)
    {
        $data = Yii::createObject(ValidationData::class);
        return $token->validate($data);
    }
}
