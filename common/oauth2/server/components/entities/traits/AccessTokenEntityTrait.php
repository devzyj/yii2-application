<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\entities\traits;

use Yii;
use yii\helpers\ArrayHelper;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Signer\Key;
use common\oauth2\server\interfaces\ClientEntityInterface;
use common\oauth2\server\interfaces\ScopeEntityInterface;
use common\oauth2\server\interfaces\UserEntityInterface;
use common\oauth2\server\components\JwtSignKey;

/**
 * AccessTokenEntityTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait AccessTokenEntityTrait
{
    use TokenEntityTrait;
    
    /**
     * @var ClientEntityInterface
     */
    private $_client;
    
    /**
     * @var ScopeEntityInterface[]
     */
    private $_scopes = [];
    
    /**
     * @var UserEntityInterface
     */
    private $_user;

    /**
     * 获取与令牌关联的客户端。
     *
     * @return ClientEntityInterface
     */
    public function getClient()
    {
        return $this->_client;
    }
    
    /**
     * 设置与令牌关联的客户端。
     * 
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->_client = $client;
    }
    
    /**
     * 获取与令牌关联的权限。
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes()
    {
        return array_values($this->_scopes);
    }

    /**
     * 添加与令牌关联的权限。
     * 
     * @param ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope)
    {
        $this->_scopes[$scope->getIdentifier()] = $scope;
    }

    /**
     * 获取与令牌关联的用户。
     *
     * @return UserEntityInterface
     */
    public function getUser()
    {
        return $this->_user;
    }
    
    /**
     * 设置与令牌关联的用户。
     *
     * @param UserEntityInterface $user
     */
    public function setUser(UserEntityInterface $user)
    {
        $this->_user = $user;
    }

    /**
     * 转换成 JWT。
     *
     * @param JwtSignKey $key
     * @return string
     */
    public function convertToJWT(JwtSignKey $key)
    {
        $scopes = ArrayHelper::getColumn($this->getScopes(), function ($element) {
            return $element->getIdentifier();
        });
        
        $builder = new Builder();
        $builder->setId($this->getIdentifier())
            ->setAudience($this->getClient()->getIdentifier())
            ->setSubject($this->getUser() ? $this->getUser()->getIdentifier() : null)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->getExpires())
            ->set('scopes', $scopes);
        
        if ($key) {
            if ($key->isSecretKey()) {
                $builder->sign(new HmacSha256(), $key->getKey());
            } elseif ($key->isPrivateKey()) {
                $builder->sign(new RsaSha256(), new Key('file://' . $key->getKey(), $key->getPassphrase()));
            }
        }
        
        return $builder->getToken();
    }
}