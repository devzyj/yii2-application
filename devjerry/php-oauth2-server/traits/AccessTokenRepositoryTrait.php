<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\traits;

use devjerry\oauth2\server\base\ArrayHelper;
use devjerry\oauth2\server\base\JwtHelper;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\exceptions\InvalidAccessTokenException;
use devjerry\oauth2\server\exceptions\ServerErrorException;

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
     * @param string|array $cryptKey 访问令牌密钥。可以是字符串密钥，或者包括以下二个元素的数组：
     *     - privateKey 私钥路径。
     *     - passphrase 私钥密码。没有密码可以为 `null`。
     * @return string 序列化的访问令牌。
     */
    public function serializeAccessTokenEntity(AccessTokenEntityInterface $accessTokenEntity, $cryptKey)
    {
        $scopes = array_map(function (ScopeEntityInterface $scopeEntity) {
            return $scopeEntity->getIdentifier();
        }, $accessTokenEntity->getScopeEntities());
        
        $client = $accessTokenEntity->getClientEntity();
        $user = $accessTokenEntity->getUserEntity();
        
        $builder = JwtHelper::createBuilder();
        $builder->setId($accessTokenEntity->getIdentifier())
            ->setAudience($client->getIdentifier())
            ->setSubject($user ? $user->getIdentifier() : null)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($accessTokenEntity->getExpires())
            ->set('scopes', $scopes);
        
        if ($cryptKey && is_string($cryptKey)) {
            $signAlg = JwtHelper::SIGN_ALG_HS256;
            $signKey = $cryptKey;
        } elseif (isset($cryptKey['privateKey'])) {
            $path = ArrayHelper::getValue($cryptKey, 'privateKey');
            $passphrase = ArrayHelper::getValue($cryptKey, 'passphrase');
            $signAlg = JwtHelper::SIGN_ALG_RS256;
            $signKey = [$path, $passphrase];
        }
        
        $builder = JwtHelper::sign($builder, $signAlg, $signKey);
        return (string) $builder->getToken();
    }

    /**
     * 反序列化访问令牌，用于从请求中接收到的访问令牌。
     *
     * 返回的实例必需要设置的属性如下：
     *     - [[setIdentifier()]]
     *     - [[setExpires()]]
     *     - [[setClientIdentifier()]]
     *     - [[setUserIdentifier()]] 如果没有用户，可以不设置。在客户端授权模式中没有用户。
     *     - [[addScopeIdentifier()]]
     * 
     * @param string $serializedAccessToken 已序列化的访问令牌。
     * @param string|array $cryptKey 访问令牌密钥。可以是字符串密钥，或者包括以下一个元素的数组：
     *     - publicKey 公钥路径。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws InvalidAccessTokenException 访问令牌无效。
     * @throws ServerErrorException 解析 JSON 数据错误。
     */
    public function unserializeAccessTokenEntity($serializedAccessToken, $cryptKey)
    {
        try {
            // 解析令牌。
            $token = JwtHelper::parseJwt($serializedAccessToken);

            // 签名密钥。
            if (isset($cryptKey['publicKey'])) {
                $signKey = [$cryptKey['publicKey']];
            } else {
                $signKey = $cryptKey;
            }

            // 验证签名。
            if (!JwtHelper::verify($token, $signKey)) {
                throw new InvalidAccessTokenException('Access token is invalid.');
            }
            
            // 验证是否过期。
            if (!JwtHelper::validateExpires($token)) {
                throw new InvalidAccessTokenException('Access token has expired.');
            }
            
            // 创建访问令牌实例。
            $accessToken = $this->createAccessTokenEntity();
            $accessToken->setIdentifier($token->getClaim('jti'));
            $accessToken->setExpires($token->getClaim('exp'));
            $accessToken->setClientIdentifier($token->getClaim('aud'));
            $accessToken->setUserIdentifier($token->getClaim('sub'));
            $scopes = $token->getClaim('scopes');
            if (is_array($scopes)) {
                foreach ($scopes as $scope) {
                    $accessToken->addScopeIdentifier($scope);
                }
            }
            
            // 返回访问令牌实例。
            return $accessToken;
        } catch (\InvalidArgumentException $exception) {
            // JWT 无法解析。
            throw new InvalidAccessTokenException($exception->getMessage(), 0, $exception);
        } catch (\RuntimeException $exception) {
            // JSON 无法解析。
            throw new ServerErrorException(500, 'Error while decoding to JSON.', 0, $exception);
        }
    }
    
    /**
     * 创建新的访问令牌实例。
     * 
     * @return AccessTokenEntityInterface 新的访问令牌实例。
     */
    abstract public function createAccessTokenEntity();
}