<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

use devjerry\oauth2\server\base\BaseObject;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;

/**
 * AuthorizeRequest class.
 * 
 * @property AuthorizeTypeInterface $authorizeType 授权类型。
 * @property ClientEntityInterface $clientEntity 客户端。
 * @property ScopeEntityInterface[] $scopeEntities 权限。
 * @property UserEntityInterface $usertEntity 用户。
 * @property boolean $isApproved 用户是否批准授权。
 * @property string $redirectUri 回调地址。
 * @property string $state 请求的状态参数。
 * @property string $codeChallenge 交换验证代码。
 * @property string $codeChallengeMethod 交换验证方法。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizeRequest extends BaseObject implements AuthorizeRequestInterface
{
    private $_authorizeType;
    private $_clientEntity;
    private $_redirectUri;
    private $_state;
    private $_scopeEntities = [];
    private $_userEntity;
    private $_isApproved;
    private $_codeChallenge;
    private $_codeChallengeMethod;
    
    /**
     * {@inheritdoc}
     */
    public function getAuthorizeType()
    {
        return $this->_authorizeType;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setAuthorizeType(AuthorizeTypeInterface $authorizeType)
    {
        $this->_authorizeType = $authorizeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity()
    {
        return $this->_clientEntity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setClientEntity(ClientEntityInterface $clientEntity)
    {
        $this->_clientEntity = $clientEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUri()
    {
        return $this->_redirectUri;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setRedirectUri($redirectUri)
    {
        $this->_redirectUri = $redirectUri;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->_state;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->_state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopeEntities()
    {
        return array_values($this->_scopeEntities);
    }

    /**
     * {@inheritdoc}
     */
    public function addScopeEntity(ScopeEntityInterface $scopeEntity)
    {
        $this->_scopeEntities[$scopeEntity->getIdentifier()] = $scopeEntity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setScopeEntities(array $scopeEntities)
    {
        $this->_scopeEntities = [];
        foreach ($scopeEntities as $scopeEntity) {
            $this->addScopeEntity($scopeEntity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUsertEntity()
    {
        return $this->_userEntity;
    }
    
    /**
     * {@inheritdoc}
    */
    public function setUserEntity(UserEntityInterface $userEntity)
    {
        $this->_userEntity = $userEntity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIsApproved()
    {
        return $this->_isApproved;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setIsApproved($approved)
    {
        $this->_isApproved = $approved;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeChallenge()
    {
        return $this->_codeChallenge;
    }
    
    /**
     * {@inheritdoc}
    */
    public function setCodeChallenge($codeChallenge)
    {
        $this->_codeChallenge = $codeChallenge;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeChallengeMethod()
    {
        return $this->_codeChallengeMethod;
    }
    
    /**
     * {@inheritdoc}
    */
    public function setCodeChallengeMethod($codeChallengeMethod)
    {
        $this->_codeChallengeMethod = $codeChallengeMethod;
    }
}