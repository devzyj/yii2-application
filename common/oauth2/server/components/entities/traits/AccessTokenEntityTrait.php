<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\entities\traits;

use Yii;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Rsa\Sha256;
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
    /**
     * @var string
     */
    private $_identifier;
    
    /**
     * @var integer
     */
    private $_expires;
    
    /**
     * @var ClientEntityInterface
     */
    private $_client;
    
    /**
     * @var ScopeEntityInterface[]
     */
    private $_scopes;
    
    /**
     * @var UserEntityInterface
     */
    private $_user;

    /**
     * 获取令牌的标识符。
     *
     * @return string 令牌的标识符。
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }
    
    /**
     * 设置令牌的标识符。
     *
     * @param string $identifier 令牌的标识符。
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = $identifier;
    }

    /**
     * 获取令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires()
    {
        return $this->_expires;
    }
    
    /**
     * 设置令牌的过期时间。
     * 
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires)
    {
        $this->_expires = $expires;
    }

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
        $builder = new Builder();
        $builder->setId($this->getIdentifier(), true)
            ->setAudience($this->getClient()->getIdentifier())
            ->setSubject($this->getUser() ? $this->getUser()->getIdentifier() : null)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->getExpires())
            ->set('scopes', $this->getScopes());
        
        if ($key) {
            $signer = new Sha256();
            if ($key->isPrivateKey()) {
                $privateKey = 'file://' . $key->getKey();
                $privateKeyPassphrase = $key->getPassphrase();
                $builder->sign($signer, new Key($privateKey, $privateKeyPassphrase));
            } elseif ($key->isSecretKey()) {
                // todo 字符串密钥加密报错。
                $builder->sign($signer, $key->getKey());
            }
        }
        
        return $builder->getToken();
    }
}