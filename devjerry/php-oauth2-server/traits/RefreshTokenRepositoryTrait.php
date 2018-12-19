<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\traits;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use devjerry\oauth2\server\interfaces\RefreshTokenEntityInterface;
use devjerry\oauth2\server\base\ArrayHelper;
use devjerry\oauth2\server\exceptions\InvalidRefreshTokenException;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;

/**
 * RefreshTokenRepositoryTrait 提供了序列化和反序列化更新令牌的方法。
 * 
 * 使用 `defuse/php-encryption` 库，加密和解密更新令牌。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait RefreshTokenRepositoryTrait
{
    /**
     * 序列化更新令牌，用于最终的响应结果。
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity 更新令牌。
     * @param array $cryptKey 更新令牌密钥。数组可以指定以下三个元素中的一个：
     *     - `ascii` 使用 `vendor/bin/generate-defuse-key` 生成的字符串。
     *     - `path` 保存了 `vendor/bin/generate-defuse-key` 生成的字符串的文件路径。
     *     - `password` 任意字符串。
     * @return string
     */
    public function serializeRefreshTokenEntity(RefreshTokenEntityInterface $refreshTokenEntity, $cryptKey)
    {
        $accessToken = $refreshTokenEntity->getAccessTokenEntity();
        $client = $refreshTokenEntity->getClientEntity();
        $user = $refreshTokenEntity->getUserEntity();
        $scopes = array_map(function (ScopeEntityInterface $scopeEntity) {
            return $scopeEntity->getIdentifier();
        }, $refreshTokenEntity->getScopeEntities());
        
        $refreshTokenData = json_encode([
            'refresh_token_id' => $refreshTokenEntity->getIdentifier(),
            'expires' => $refreshTokenEntity->getExpires(),
            'access_token_id' => $accessToken->getIdentifier(),
            'client_id' => $client->getIdentifier(),
            'user_id' => $user ? $user->getIdentifier() : null,
            'scopes' => $scopes,
        ]);
        
        // 加密数据。
        if (isset($cryptKey['ascii'])) {
            $key = Key::loadFromAsciiSafeString($cryptKey['ascii']);
            return Crypto::encrypt($refreshTokenData, $key);
        } elseif (isset($cryptKey['path'])) {
            $ascii = file_get_contents($cryptKey['path']);
            $key = Key::loadFromAsciiSafeString($ascii);
            return Crypto::encrypt($refreshTokenData, $key);
        } elseif (isset($cryptKey['password'])) {
            $key = $cryptKey['password'];
            return Crypto::encryptWithPassword($refreshTokenData, $key);
        } else {
            return $refreshTokenData;
        }
    }

    /**
     * 反序列化更新令牌，用于从请求中接收到的更新令牌。
     *
     * 返回的实例必需要设置的属性如下：
     *     - [[setIdentifier()]]
     *     - [[setExpires()]]
     *     - [[setAccessTokenIdentifier()]]
     *     - [[setClientIdentifier()]]
     *     - [[setUserIdentifier()]] 如果没有用户，可以不设置。在客户端授权模式中没有用户。
     *     - [[addScopeIdentifier()]]
     * 
     * @param string $serializedRefreshToken 已序列化的更新令牌。
     * @param array $cryptKey 更新令牌密钥。数组可以指定以下三个元素中的一个：
     *     - `ascii` 使用 `vendor/bin/generate-defuse-key` 生成的字符串。
     *     - `path` 保存了 `vendor/bin/generate-defuse-key` 生成的字符串的文件路径。
     *     - `password` 任意字符串。
     * @return RefreshTokenEntityInterface 更新令牌实例。
     * @throws InvalidRefreshTokenException 更新令牌无效。
     */
    public function unserializeRefreshTokenEntity($serializedRefreshToken, $cryptKey)
    {
        try {
            // 解密数据。
            if (isset($cryptKey['ascii'])) {
                $key = Key::loadFromAsciiSafeString($cryptKey['ascii']);
                $serializedRefreshToken = Crypto::decrypt($serializedRefreshToken, $key);
            } elseif (isset($cryptKey['path'])) {
                $ascii = file_get_contents($cryptKey['path']);
                $key = Key::loadFromAsciiSafeString($ascii);
                $serializedRefreshToken = Crypto::decrypt($serializedRefreshToken, $key);
            } elseif (isset($cryptKey['password'])) {
                $key = $cryptKey['password'];
                $serializedRefreshToken = Crypto::decryptWithPassword($serializedRefreshToken, $key);
            }
            
            $data = json_decode($serializedRefreshToken, true);
            if (empty($data)) {
                return null;
            }
            
            // 创建更新令牌实例。
            $refreshToken = $this->createRefreshTokenEntity();
            $refreshToken->setIdentifier(ArrayHelper::getValue($data, 'refresh_token_id'));
            $refreshToken->setExpires(ArrayHelper::getValue($data, 'expires'));
            $refreshToken->setAccessTokenIdentifier(ArrayHelper::getValue($data, 'access_token_id'));
            $refreshToken->setClientIdentifier(ArrayHelper::getValue($data, 'client_id'));
            $refreshToken->setUserIdentifier(ArrayHelper::getValue($data, 'user_id'));
            $scopes = ArrayHelper::getValue($data, 'scopes', []);
            if (is_array($scopes)) {
                foreach ($scopes as $scope) {
                    $refreshToken->addScopeIdentifier($scope);
                }
            }
            
            return $refreshToken;
        } catch (WrongKeyOrModifiedCiphertextException $exception) {
            throw new InvalidRefreshTokenException('Refresh token is invalid.', 0, $exception);
        }
    }
    
    /**
     * 创建新的更新令牌实例。
     * 
     * @return RefreshTokenEntityInterface 新的更新令牌实例。
     */
    abstract public function createRefreshTokenEntity();
}