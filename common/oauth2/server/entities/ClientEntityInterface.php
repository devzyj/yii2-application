<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities;

/**
 * 客户端实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface ClientEntityInterface
{
    /**
     * 获取标识符。
     * 
     * @return string
     */
    public function getIdentifier();
    
    /**
     * 获取密钥。
     * 
     * @return string
     */
    public function getSecret();
    
    /**
     * 获取授权类型。
     * 
     * @return string[]
     */
    public function getGrantTypes();

    /**
     * 获取权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes();
    
    /**
     * 获取访问令牌的持续时间（秒）。
     * 
     * @return integer
     */
    public function getAccessTokenDuration();

    /**
     * 获取更新令牌的持续时间（秒）。
     *
     * @return integer
     */
    public function getRefreshTokenDuration();

    /**
     * 获取私钥。
     *
     * @return array
     */
    public function getPrivateKey();
    
    /**
     * 获取回调地址。
     * 
     * @return string|string[]
     
    public function getRedirectUri();*/
    
    /**
     * 获取加密键。
     * 
     * @return string
     
    public function getEncryptionKey();*/
}