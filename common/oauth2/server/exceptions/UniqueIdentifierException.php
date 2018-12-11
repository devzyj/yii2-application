<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\exceptions;

/**
 * UniqueIdentifierException 保存令牌时，唯一标识重复的异常。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UniqueIdentifierException extends \yii\web\ServerErrorHttpException
{
    /**
     * Constructor.
     * 
     * @param string $message error message
     * @param int $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        if ($message === null) {
            $message = 'Could not create unique identifier.';
        }
        
        parent::__construct($message, $code, $previous);
    }
}
