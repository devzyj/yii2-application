<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories\traits;

use Yii;
use yii\helpers\ArrayHelper;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Signer\Key;
use common\oauth2\server\interfaces\AccessTokenEntityInterface;

/**
 * AccessTokenRepositoryTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait AccessTokenRepositoryTrait
{
    /**
     * 序列化访问令牌，用于最终的响应结果。
     *
     * @param AccessTokenEntityInterface $accessTokenEntity 访问令牌。
     * @param mixed $cryptKey 访问令牌密钥。
     * @return string 序列化的访问令牌。
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
            $builder->sign(new RsaSha256(), new Key('file://' . Yii::getAlias($path), $passphrase));
        }
        
        return (string) $builder->getToken();
    }

    /**
     * 反序列化访问令牌，用于从请求中接收到的访问令牌。
     *
     * @param string $serializedAccessToken 已序列化的访问令牌。
     * @param mixed $cryptKey 访问令牌密钥。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws UnauthorizedHttpException 无效的访问令牌。
     */
    public function unserializeAccessTokenEntity($serializedAccessToken, $cryptKey)
    {
        
    }
    
}