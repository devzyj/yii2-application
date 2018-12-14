<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\interfaces;

/**
 * 更新令牌实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface RefreshTokenEntityInterface extends BaseTokenCodeEntityInterface
{
    /**
     * 获取关联的访问令牌标识。
     *
     * @param string
     */
    public function getAccessTokenIdentifier();
    
    /**
     * 设置关联的访问令牌标识。
     * 
     * @param string $accessTokenIdentifier
     */
    public function setAccessTokenIdentifier($accessTokenIdentifier);
    
    /**
     * 获取关联的访问令牌。
     *
     * @return AccessTokenEntityInterface
     */
    public function getAccessTokenEntity();
    
    /**
     * 设置关联的访问令牌。
     * 
     * @param AccessTokenEntityInterface $accessTokenEntity
     */
    public function setAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity);
}