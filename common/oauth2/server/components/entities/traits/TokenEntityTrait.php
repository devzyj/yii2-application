<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\entities\traits;

/**
 * TokenEntityTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait TokenEntityTrait
{
    use EntityTrait;
    
    /**
     * @var integer
     */
    private $_expires;
    
    /**
     * 获取令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires()
    {
        return $this->_expires;
    }
    
    /**
     * 设置令牌的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires)
    {
        $this->_expires = $expires;
    }
}