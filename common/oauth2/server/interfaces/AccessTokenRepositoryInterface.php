<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

use yii\web\UnauthorizedHttpException;
use common\oauth2\server\exceptions\UniqueTokenIdentifierException;

/**
 * 访问令牌存储库接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface AccessTokenRepositoryInterface
{
    /**
     * 创建新的访问令牌实例。
     * 
     * @return AccessTokenEntityInterface 新的访问令牌实例。
     */
    public function createAccessTokenEntity();
    
    /**
     * 保存访问令牌。
     * 
     * @param AccessTokenEntityInterface $accessTokenEntity 访问令牌。
     * @throws UniqueTokenIdentifierException 访问令牌标识重复。
     */
    public function saveAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity);
    
    /**
     * 撤销访问令牌。
     * 
     * @param string $identifier 访问令牌标识。
     * @return boolean 撤销是否成功。
     */
    public function revokeAccessTokenEntity($identifier);

    /**
     * 访问令牌是否已撤销。
     *
     * @param string $identifier 访问令牌标识。
     * @return boolean 是否已撤销。
     */
    public function isAccessTokenEntityRevoked($identifier);

    /**
     * 序列化访问令牌，用于最终的响应结果。
     *
     * @param AccessTokenEntityInterface $accessTokenEntity 访问令牌。
     * @param mixed $cryptKey 访问令牌密钥。
     * @return string 序列化的访问令牌。
     */
    public function serializeAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity, $cryptKey);
    
    /**
     * 反序列化访问令牌，用于从请求中接收到的访问令牌。
     *
     * @param string $serializedAccessToken 已序列化的访问令牌。
     * @param mixed $cryptKey 访问令牌密钥。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws UnauthorizedHttpException 无效的访问令牌。
     */
    public function unserializeAccessTokenEntity($serializedAccessToken, $cryptKey);
}