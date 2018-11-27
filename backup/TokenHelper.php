<?php
/**
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 */
namespace backup\helpers;

/**
 * 令牌帮助类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 * @deprecated
 */
class TokenHelper
{
    /**
     * @var string 令牌信息中的分隔符。
     */
    const TOKEN_SEPARATOR = '.';

    /**
     * @var string 请求 URL 中传递令牌的参数名。
     */
    const TOKEN_QUERY_PARAM = 'access-token';
    
    /**
     * 获取 http headers 中的 Authorization。
     * 
     * @param \yii\web\Request $request
     * @return string
     */
    public static function getHttpBearerAuth($request)
    {
        $header = $request->getHeaders()->get('Authorization');
        $pattern = '/^Bearer\s+(.*?)$/';
        
        if ($header !== null) {
            if (preg_match($pattern, $header, $matches)) {
                return $matches[1];
            }
        }
    }
    
    /**
     * 获取 http url 中的 令牌信息。
     * 
     * @param \yii\web\Request $request
     * @return string
     */
    public static function getHttpQueryAuth($request)
    {
        return $request->getQueryParam(self::TOKEN_QUERY_PARAM);
    }
    
    /**
     * 获取请求中的令牌信息。
     * 
     * @param \yii\web\Request $request
     * @return string
     */
    public static function getRequestToken($request)
    {
        $token = static::getHttpBearerAuth($request);
        if (empty($token)) {
            $token = static::getHttpQueryAuth($request);
        }
        
        return $token;
    }
    
    /**
     * 解析客户端标识。
     * 
     * @param string $token
     * @return string
     */
    public static function parseClientIdentifier($token)
    {
        $values = explode(self::TOKEN_SEPARATOR, $token);
        return isset($values[0]) ? $values[0] : '';
    }
    
    /**
     * 解析时间戳。
     * 
     * @param string $token
     * @return integer
     */
    public static function parseTimestamp($token)
    {
        $values = explode(self::TOKEN_SEPARATOR, $token);
        return isset($values[1]) ? (int) $values[1] : 0;
    }
    
    /**
     * 解析签名。
     * 
     * @param string $token
     * @return string
     */
    public static function parseSignature($token)
    {
        $values = explode(self::TOKEN_SEPARATOR, $token);
        return isset($values[2]) ? $values[2] : '';
    }
}
