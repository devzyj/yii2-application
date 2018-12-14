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
use devjerry\oauth2\server\interfaces\AuthorizationCodeEntityInterface;
use devjerry\oauth2\server\exceptions\OAuthServerException;
use devjerry\oauth2\server\base\ArrayHelper;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;

/**
 * AuthorizationCodeRepositoryTrait 提供了序列化和反序列化授权码的方法。
 * 
 * 使用 `defuse/php-encryption` 库，加密和解密授权码。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait AuthorizationCodeRepositoryTrait
{
    /**
     * 序列化授权码，用于最终的响应结果。
     *
     * @param AuthorizationCodeEntityInterface $authorizationCodeEntity 授权码。
     * @param mixed $cryptKey 授权码密钥。数组可以指定以下三个元素中的一个：
     *     - `ascii` 使用 `vendor/bin/generate-defuse-key` 生成的字符串。
     *     - `path` 保存了 `vendor/bin/generate-defuse-key` 生成的字符串的文件路径。
     *     - `password` 任意字符串。
     * @return string 序列化的授权码。
     */
    public function serializeAuthorizationCodeEntity(AuthorizationCodeEntityInterface $authorizationCodeEntity, $cryptKey)
    {
        $client = $authorizationCodeEntity->getClientEntity();
        $user = $authorizationCodeEntity->getUserEntity();
        $scopes = array_map(function (ScopeEntityInterface $scopeEntity) {
            return $scopeEntity->getIdentifier();
        }, $authorizationCodeEntity->getScopeEntities());
        
        // 授权码数据。
        $authorizationCodeData = json_encode([
            'authorization_code_id' => $authorizationCodeEntity->getIdentifier(),
            'expires' => $authorizationCodeEntity->getExpires(),
            'redirect_uri' => $authorizationCodeEntity->getRedirectUri(),
            'client_id' => $client->getIdentifier(),
            'user_id' => $user->getIdentifier(),
            'scopes' => $scopes,
            'code_challenge' => $authorizationCodeEntity->getCodeChallenge(),
            'code_challenge_method' => $authorizationCodeEntity->getCodeChallengeMethod(),
        ]);
        
        // 加密数据。
        if (isset($cryptKey['ascii'])) {
            $key = Key::loadFromAsciiSafeString($cryptKey['ascii']);
            return Crypto::encrypt($authorizationCodeData, $key);
        } elseif (isset($cryptKey['path'])) {
            $ascii = file_get_contents($cryptKey['path']);
            $key = Key::loadFromAsciiSafeString($ascii);
            return Crypto::encrypt($authorizationCodeData, $key);
        } elseif (isset($cryptKey['password'])) {
            $key = $cryptKey['password'];
            return Crypto::encryptWithPassword($authorizationCodeData, $key);
        } else {
            return $authorizationCodeData;
        }
    }

    /**
     * 反序列化授权码，用于从请求中接收到的授权码。
     *
     * 返回的实例必需要设置的属性如下：
     *     - [[setIdentifier()]]
     *     - [[setExpires()]]
     *     - [[setRedirectUri()]]
     *     - [[setClientIdentifier()]]
     *     - [[setUserIdentifier()]]
     *     - [[addScopeIdentifier()]]
     * 
     * @param string $serializedAuthorizationCode 已序列化的授权码。
     * @param mixed $cryptKey 授权码密钥。数组可以指定以下三个元素中的一个：
     *     - `ascii` 使用 `vendor/bin/generate-defuse-key` 生成的字符串。
     *     - `path` 保存了 `vendor/bin/generate-defuse-key` 生成的字符串的文件路径。
     *     - `password` 任意字符串。
     * @return AuthorizationCodeEntityInterface 授权码实例。
     * @throws OAuthServerException 授权码无效。
     */
    public function unserializeAuthorizationCodeEntity($serializedAuthorizationCode, $cryptKey)
    {
        try {
            // 解密数据。
            if (isset($cryptKey['ascii'])) {
                $key = Key::loadFromAsciiSafeString($cryptKey['ascii']);
                $serializedAuthorizationCode = Crypto::decrypt($serializedAuthorizationCode, $key);
            } elseif (isset($cryptKey['path'])) {
                $ascii = file_get_contents($cryptKey['path']);
                $key = Key::loadFromAsciiSafeString($ascii);
                $serializedAuthorizationCode = Crypto::decrypt($serializedAuthorizationCode, $key);
            } elseif (isset($cryptKey['password'])) {
                $key = $cryptKey['password'];
                $serializedAuthorizationCode = Crypto::decryptWithPassword($serializedAuthorizationCode, $key);
            }
            
            $data = json_decode($serializedAuthorizationCode, true);
            if (empty($data)) {
                return null;
            }
            
            // 创建授权码实例。
            $authorizationCode = $this->createAuthorizationCodeEntity();
            $authorizationCode->setIdentifier(ArrayHelper::getValue($data, 'authorization_code_id'));
            $authorizationCode->setExpires(ArrayHelper::getValue($data, 'expires'));
            $authorizationCode->setRedirectUri(ArrayHelper::getValue($data, 'redirect_uri'));
            $authorizationCode->setClientIdentifier(ArrayHelper::getValue($data, 'client_id'));
            $authorizationCode->setUserIdentifier(ArrayHelper::getValue($data, 'user_id'));
            $authorizationCode->setCodeChallenge(ArrayHelper::getValue($data, 'code_challenge'));
            $authorizationCode->setCodeChallengeMethod(ArrayHelper::getValue($data, 'code_challenge_method'));
            $scopes = ArrayHelper::getValue($data, 'scopes', []);
            if ($scopes && is_array($scopes)) {
                foreach ($scopes as $scope) {
                    $authorizationCode->addScopeIdentifier($scope);
                }
            }
            
            return $authorizationCode;
        } catch (WrongKeyOrModifiedCiphertextException $e) {
            throw new OAuthServerException(401, 'Authorization code is invalid.', 0, $e);
        }
    }

    /**
     * 创建新的授权码实例。
     *
     * @return AuthorizationCodeEntityInterface 新的授权码实例。
     */
    abstract public function createAuthorizationCodeEntity();
}