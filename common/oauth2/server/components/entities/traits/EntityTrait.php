<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\entities\traits;

/**
 * EntityTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait EntityTrait
{
    /**
     * @var string
     */
    private $_identifier;
    
    /**
     * 获取标识符。
     *
     * @return string 标识符。
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }
    
    /**
     * 设置标识符。
     *
     * @param string $identifier 标识符。
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = $identifier;
    }
}