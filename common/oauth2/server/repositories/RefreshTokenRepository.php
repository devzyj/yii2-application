<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories;

use Yii;
use yii\helpers\Json;
use yii\base\InvalidArgumentException;
use yii\web\UnauthorizedHttpException;
use common\oauth2\server\interfaces\RefreshTokenRepositoryInterface;
use common\oauth2\server\interfaces\RefreshTokenEntityInterface;
use common\oauth2\server\entities\RefreshTokenEntity;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use yii\helpers\ArrayHelper;

/**
 * RefreshTokenRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRefreshTokenEntity()
    {
        return Yii::createObject(RefreshTokenEntity::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveRefreshTokenEntity(RefreshTokenEntityInterface $refreshTokenEntity)
    {}
    
    /**
     * {@inheritdoc}
     */
    public function revokeRefreshTokenEntity($identifier)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenEntityRevoked($identifier)
    {
        return false;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     * 
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
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            throw new UnauthorizedHttpException('Invalid refresh token.', 0, $e);
        } catch (InvalidArgumentException $e) {
            throw new UnauthorizedHttpException('Invalid json.', 0, $e);
        }
        
        // 创建更新令牌实例。
        $refreshToken = $this->createRefreshTokenEntity();
        $refreshToken->setIdentifier(ArrayHelper::getValue($data, 'refresh_token_id'));
        $refreshToken->setExpires(ArrayHelper::getValue($data, 'expires'));
        $refreshToken->setClientIdentifier(ArrayHelper::getValue($data, 'client_id'));
        $refreshToken->setUserIdentifier(ArrayHelper::getValue($data, 'user_id'));
        $scopes = ArrayHelper::getValue($data, 'scopes', []);
        if ($scopes && is_array($scopes)) {
            foreach ($scopes as $scope) {
                $refreshToken->addScopeIdentifier($scope);
            }
        }
        
        return $refreshToken;
    }
}