<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

/**
 * AuthorizeGrantPropertyTrait
 *
 * @property string[] $defaultScopes 默认权限。
 * @property mixed $accessTokenCryptKey 访问令牌密钥。
 * @property integer $accessTokenDuration 访问令牌持续时间（秒）。
 * @property mixed $authorizationCodeCryptKey 授权码密钥。
 * @property integer $authorizationCodeDuration 授权码持续时间（秒）。
 * @property mixed $refreshTokenCryptKey 更新令牌密钥。
 * @property integer $refreshTokenDuration 更新令牌持续时间（秒）。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait AuthorizeGrantPropertyTrait
{
    /**
     * @var string[] 默认权限。
     */
    private $_defaultScopes;

    /**
     * @var mixed 访问令牌密钥。
     */
    private $_accessTokenCryptKey;
    
    /**
     * @var integer 访问令牌持续时间（秒）。
     */
    private $_accessTokenDuration;

    /**
     * @var mixed 授权码密钥。
     */
    private $_authorizationCodeCryptKey;
    
    /**
     * @var integer 授权码持续时间（秒）。
     */
    private $_authorizationCodeDuration;
    
    /**
     * @var mixed 更新令牌密钥。
     */
    private $_refreshTokenCryptKey;

    /**
     * @var integer 更新令牌持续时间（秒）。
     */
    private $_refreshTokenDuration;

    /**
     * 获取默认权限。
     *
     * @return string[]
     */
    public function getDefaultScopes()
    {
        return $this->_defaultScopes;
    }
    
    /**
     * 设置默认权限。
     *
     * @param string[] $scopes
     */
    public function setDefaultScopes(array $scopes)
    {
        $this->_defaultScopes = $scopes;
    }

    /**
     * 获取访问令牌密钥。
     *
     * @return mixed
     */
    public function getAccessTokenCryptKey()
    {
        return $this->_accessTokenCryptKey;
    }
    
    /**
     * 设置访问令牌密钥。
     *
     * @param mixed $key
     */
    public function setAccessTokenCryptKey($key)
    {
        $this->_accessTokenCryptKey = $key;
    }

    /**
     * 获取访问令牌持续时间。
     *
     * @return integer 持续的秒数。
     */
    public function getAccessTokenDuration()
    {
        return $this->_accessTokenDuration;
    }
    
    /**
     * 设置访问令牌持续时间。
     *
     * @param integer $duration 以秒为单位的持续时间。
     */
    public function setAccessTokenDuration($duration)
    {
        $this->_accessTokenDuration = $duration;
    }

    /**
     * 获取授权码密钥。
     *
     * @return mixed
     */
    public function getAuthorizationCodeCryptKey()
    {
        return $this->_authorizationCodeCryptKey;
    }
    
    /**
     * 设置授权码密钥。
     *
     * @param mixed $key
     */
    public function setAuthorizationCodeCryptKey($key)
    {
        $this->_authorizationCodeCryptKey = $key;
    }
    
    /**
     * 获取授权码持续时间。
     *
     * @return integer 持续的秒数。
     */
    public function getAuthorizationCodeDuration()
    {
        return $this->_authorizationCodeDuration;
    }
    
    /**
     * 设置授权码持续时间。
     *
     * @param integer $duration 以秒为单位的持续时间。
     */
    public function setAuthorizationCodeDuration($duration)
    {
        $this->_authorizationCodeDuration = $duration;
    }
    
    /**
     * 获取更新令牌密钥。
     *
     * @return mixed
     */
    public function getRefreshTokenCryptKey()
    {
        return $this->_refreshTokenCryptKey;
    }
    
    /**
     * 设置更新令牌密钥。
     *
     * @param mixed $key
     */
    public function setRefreshTokenCryptKey($key)
    {
        $this->_refreshTokenCryptKey = $key;
    }

    /**
     * 获取更新令牌持续时间。
     *
     * @return integer 持续的秒数。
     */
    public function getRefreshTokenDuration()
    {
        return $this->_refreshTokenDuration;
    }
    
    /**
     * 设置更新令牌持续时间。
     *
     * @param integer $duration 以秒为单位的持续时间。
     */
    public function setRefreshTokenDuration($duration)
    {
        $this->_refreshTokenDuration = $duration;
    }
}