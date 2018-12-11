<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\repositories\traits;

use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\base\InvalidArgumentException;
use yii\web\UnauthorizedHttpException;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use devjerry\oauth2\server\interfaces\RefreshTokenEntityInterface;

/**
 * RefreshTokenRepositoryTrait
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
     * @param mixed $cryptKey 更新令牌密钥。
     * @return string
     */
    public function serializeRefreshTokenEntity(RefreshTokenEntityInterface $refreshTokenEntity, $cryptKey)
    {
        $accessToken = $refreshTokenEntity->getAccessTokenEntity();
        
        $refreshTokenData = Json::encode([
            'refresh_token_id' => $refreshTokenEntity->getIdentifier(),
            'access_token_id' => $accessToken->getIdentifier(),
            'client_id' => $refreshTokenEntity->getClientIdentifier(),
            'user_id' => $refreshTokenEntity->getUserIdentifier() ? $refreshTokenEntity->getUserIdentifier() : null,
            'scopes' => $refreshTokenEntity->getScopeIdentifiers(),
            'expires' => $refreshTokenEntity->getExpires(),
        ]);
        
        // 加密数据。
        if (isset($cryptKey['ascii'])) {
            $key = Key::loadFromAsciiSafeString($cryptKey['ascii']);
            return Crypto::encrypt($refreshTokenData, $key);
        } elseif (isset($cryptKey['path'])) {
            $ascii = file_get_contents(Yii::getAlias($cryptKey['path']));
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
     * @param string $serializedRefreshToken 已序列化的更新令牌。
     * @param mixed $cryptKey 更新令牌密钥。
     * @return RefreshTokenEntityInterface 更新令牌实例。
     * @throws UnauthorizedHttpException 更新令牌无效。
     */
    public function unserializeRefreshTokenEntity($serializedRefreshToken, $cryptKey)
    {
        try {
            // 解密数据。
            if (isset($cryptKey['ascii'])) {
                $key = Key::loadFromAsciiSafeString($cryptKey['ascii']);
                $serializedRefreshToken = Crypto::decrypt($serializedRefreshToken, $key);
            } elseif (isset($cryptKey['path'])) {
                $ascii = file_get_contents(Yii::getAlias($cryptKey['path']));
                $key = Key::loadFromAsciiSafeString($ascii);
                $serializedRefreshToken = Crypto::decrypt($serializedRefreshToken, $key);
            } elseif (isset($cryptKey['password'])) {
                $key = $cryptKey['password'];
                $serializedRefreshToken = Crypto::decryptWithPassword($serializedRefreshToken, $key);
            }
            
            $data = Json::decode($serializedRefreshToken);
            
            // 创建更新令牌实例。
            $refreshToken = $this->createRefreshTokenEntity();
            $refreshToken->setIdentifier(ArrayHelper::getValue($data, 'refresh_token_id'));
            $refreshToken->setExpires(ArrayHelper::getValue($data, 'expires'));
            $refreshToken->setAccessTokenIdentifier(ArrayHelper::getValue($data, 'access_token_id'));
            $refreshToken->setClientIdentifier(ArrayHelper::getValue($data, 'client_id'));
            $refreshToken->setUserIdentifier(ArrayHelper::getValue($data, 'user_id'));
            $scopes = ArrayHelper::getValue($data, 'scopes', []);
            if ($scopes && is_array($scopes)) {
                foreach ($scopes as $scope) {
                    $refreshToken->addScopeIdentifier($scope);
                }
            }
            
            return $refreshToken;
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            throw new UnauthorizedHttpException('Refresh token is invalid.', 0, $e);
        } catch (InvalidArgumentException $e) {
            throw new UnauthorizedHttpException('Refresh token is invalid.', 0, $e);
        }
    }
}