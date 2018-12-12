<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\traits;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Signer\Key;
use devjerry\oauth2\server\base\ArrayHelper;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;

/**
 * AccessTokenRepositoryTrait 提供了序列化和反序列化访问令牌的方法。
 *
 * 使用 `lcobucci/jwt` 库，序列化和反序列化访问令牌。
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
     * @param string|array $cryptKey 访问令牌密钥。可以是字符串密钥，或者包括以下三个元素的数组：
     *     - privateKey 私钥路径。
     *     - passphrase 私钥密码。
     *     - publicKey 公钥路径。
     * @return string 序列化的访问令牌。
     */
    public function serializeAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity, $cryptKey)
    {
        $scopes = [];
        foreach ($accessTokenEntity->getScopeEntities() as $accessTokenScopes) {
            $scopes[] = $accessTokenScopes->getIdentifier();
        }
        
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
     * 反序列化访问令牌，用于从请求中接收到的访问令牌。
     *
     * @param string $serializedAccessToken 已序列化的访问令牌。
     * @param string|array $cryptKey 访问令牌密钥。可以是字符串密钥，或者包括以下三个元素的数组：
     *     - privateKey 私钥路径。
     *     - passphrase 私钥密码。
     *     - publicKey 公钥路径。
     * @return AccessTokenEntityInterface 访问令牌实例。
     */
    public function unserializeAccessTokenEntity($serializedAccessToken, $cryptKey)
    {
        
    }
    
}