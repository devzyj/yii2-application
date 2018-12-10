<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories;

use Yii;
use yii\helpers\ArrayHelper;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Signer\Key;
use common\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use common\oauth2\server\interfaces\AccessTokenEntityInterface;
use common\oauth2\server\entities\AccessTokenEntity;

/**
 * AccessTokenRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     * 
     * @return AccessTokenEntity 新的访问令牌实例。
     */
    public function createAccessTokenEntity()
    {
        return Yii::createObject(AccessTokenEntity::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity)
    {}
    
    /**
     * {@inheritdoc}
     */
    public function revokeAccessTokenEntity($identifier)
    {}

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenEntityRevoked($identifier)
    {}

    /**
     * {@inheritdoc}
     */
    public function serializeAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity, $cryptKey)
    {
        $scopes = ArrayHelper::getColumn($accessTokenEntity->getScopeEntities(), function ($element) {
            return $element->getIdentifier();
        });
        
        $client = $accessTokenEntity->getClientEntity();
        $user = $accessTokenEntity->getUserEntity();
        
        $builder = new Builder();
        $builder->setId($accessTokenEntity->getIdentifier())
            ->setAudience($client->getIdentifier())
            ->setSubject($user ? $user->getIdentifier() : null)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($accessTokenEntity->getExpires())
            ->set('scopes', $scopes);
        
        if ($cryptKey && is_string($cryptKey)) {
            $builder->sign(new HmacSha256(), $cryptKey);
        } elseif (isset($cryptKey['privateKey'])) {
            $path = ArrayHelper::getValue($cryptKey, 'privateKey');
            $passphrase = ArrayHelper::getValue($cryptKey, 'passphrase');
            $builder->sign(new RsaSha256(), new Key('file://' . $path, $passphrase));
        }
        
        return (string) $builder->getToken();
    }
    
    /**
     * {@inheritdoc}
     */
    public function unserializeAccessTokenEntity($serializedAccessToken, $cryptKey)
    {
        
    }
}