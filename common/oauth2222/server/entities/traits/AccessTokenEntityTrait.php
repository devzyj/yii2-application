<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities\traits;

use common\oauth2\server\interfaces\ClientEntityInterface;
use common\oauth2\server\interfaces\UserEntityInterface;
use common\oauth2\server\interfaces\ScopeEntityInterface;

/**
 * AccessTokenEntityTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait AccessTokenEntityTrait
{
    use EntityTrait;

    /**
     * @var integer
     */
    private $_expires;

    /**
     * @var ClientEntityInterface
     */
    private $_clientEntity;

    /**
     * @var UserEntityInterface
     */
    private $_userEntity;

    /**
     * @var ScopeEntityInterface[]
     */
    private $_scopeEntities = [];

    /**
     * 获取访问令牌的过期时间。
     *
     * @return integer 过期的时间戳。
     */
    public function getExpires()
    {
        return $this->_expires;
    }

    /**
     * 设置访问令牌的过期时间。
     *
     * @param integer $expires 过期时间的时间戳。
     */
    public function setExpires($expires)
    {
        $this->_expires = $expires;
    }

    /**
     * 获取与访问令牌关联的客户端。
     *
     * @return ClientEntityInterface 客户端实例。
     */
    public function getClientEntity()
    {
        return $this->_clientEntity;
    }

    /**
     * 设置与访问令牌关联的客户端。
     *
     * @param ClientEntityInterface $clientEntity 客户端实例。
     */
    public function setClientEntity(ClientEntityInterface $clientEntity)
    {
        $this->_clientEntity = $clientEntity;
    }

    /**
     * 获取与访问令牌关联的用户。
     *
     * @return UserEntityInterface 用户实例。
     */
    public function getUserEntity()
    {
        return $this->_userEntity;
    }

    /**
     * 设置与访问令牌关联的用户。
     *
     * @param UserEntityInterface $userEntity 用户实例。
     */
    public function setUserEntity(UserEntityInterface $userEntity)
    {
        $this->_userEntity = $userEntity;
    }

    /**
     * 获取与访问令牌关联的权限。
     *
     * @return ScopeEntityInterface[] 权限实例列表。
     */
    public function getScopeEntities()
    {
        return array_values($this->_scopeEntities);
    }

    /**
     * 添加与访问令牌关联的权限。
     * 
     * @param ScopeEntityInterface $scopeEntity 权限实例。
     */
    public function addScopeEntity(ScopeEntityInterface $scopeEntity)
    {
        $this->_scopeEntities[$scopeEntity->getIdentifier()] = $scopeEntity;
    }
}