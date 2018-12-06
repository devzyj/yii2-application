<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 更新令牌实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface RefreshTokenEntityInterface
{
    /**
     * 获取令牌的标识符。
     *
     * @return string 令牌的标识符。
     */
    public function getIdentifier();
    
    /**
     * 设置令牌的标识符。
     *
     * @param string $identifier 令牌的标识符。
     */
    public function setIdentifier($identifier);

    /**
     * 获取令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires();
    
    /**
     * 设置令牌的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires);
    
    /**
     * 获取与更新令牌关联的访问令牌。
     *
     * @return AccessTokenEntityInterface
     */
    public function getAccessToken();
    
    /**
     * 设置与更新令牌关联的访问令牌。
     * 
     * @param AccessTokenEntityInterface $token
     */
    public function setAccessToken(AccessTokenEntityInterface $token);
}