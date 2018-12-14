<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\traits;

use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;

/**
 * RefreshTokenEntityTrait 实现了 [[RefreshTokenEntityInterface]] 中的方法。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait RefreshTokenEntityTrait
{
    use BaseTokenCodeEntityTrait;
    
    /**
     * @var string
     */
    private $_accessTokenIdentifier;
    
    /**
     * @var AccessTokenEntityInterface
     */
    private $_accessTokenEntity;

    /**
     * 获取关联的访问令牌标识。
     *
     * @param string
     */
    public function getAccessTokenIdentifier()
    {
        return $this->_accessTokenIdentifier;
    }
    
    /**
     * 设置关联的访问令牌标识。
     * 
     * @param string $accessTokenIdentifier
     */
    public function setAccessTokenIdentifier($accessTokenIdentifier)
    {
        $this->_accessTokenIdentifier = $accessTokenIdentifier;
    }
    
    /**
     * 获取关联的访问令牌。
     *
     * @return AccessTokenEntityInterface
     */
    public function getAccessTokenEntity()
    {
        return $this->_accessTokenEntity;
    }
    
    /**
     * 设置关联的访问令牌。
     * 
     * @param AccessTokenEntityInterface $accessTokenEntity
     */
    public function setAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->_accessTokenEntity = $accessTokenEntity;
    }
}