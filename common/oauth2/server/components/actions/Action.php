<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\actions;

use Yii;
use yii\base\InvalidConfigException;
use common\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use common\oauth2\server\interfaces\AuthCodeRepositoryInterface;
use common\oauth2\server\interfaces\ClientRepositoryInterface;
use common\oauth2\server\interfaces\RefreshTokenRepositoryInterface;
use common\oauth2\server\interfaces\ScopeRepositoryInterface;
use common\oauth2\server\interfaces\UserRepositoryInterface;

/**
 * Action class.
 * 
 * @property AccessTokenRepositoryInterface $accessTokenRepository 访问令牌存储。
 * @property AuthCodeRepositoryInterface $authCodeRepository 授权码存储。
 * @property ClientRepositoryInterface $clientRepository 客户端存储。
 * @property RefreshTokenRepositoryInterface $refreshTokenRepository 更新令牌存储。
 * @property ScopeRepositoryInterface $scopeRepository 权限存储。
 * @property UserRepositoryInterface $userRepository 用户存储。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class Action extends \yii\base\Action
{
    /**
     * @var \yii\web\Request 当前的请求。如果没有设置，将使用 `Yii::$app->getRequest()`。
     */
    public $request;
    
    /**
     * @var \yii\web\Response 要发送的响应。如果没有设置，将使用 `Yii::$app->getResponse()`。
     */
    public $response;
    
    /**
     * @var AccessTokenRepositoryInterface 访问令牌存储。
     */
    private $_accessTokenRepository;

    /**
     * @var AuthCodeRepositoryInterface 授权码存储。
     */
    private $_authCodeRepository;
    
    /**
     * @var ClientRepositoryInterface 客户端存储。
     */
    private $_clientRepository;

    /**
     * @var RefreshTokenRepositoryInterface 更新令牌存储。
     */
    private $_refreshTokenRepository;
    
    /**
     * @var ScopeRepositoryInterface 权限存储。
     */
    private $_scopeRepository;

    /**
     * @var UserRepositoryInterface 用户存储。
     */
    private $_userRepository;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->request === null) {
            $this->request = Yii::$app->getRequest();
        }
        
        if ($this->response === null) {
            $this->response = Yii::$app->getResponse();
        }
    }
    
    /**
     * 获取访问令牌存储。
     *
     * @return AccessTokenRepositoryInterface
     */
    public function getAccessTokenRepository()
    {
        return $this->_accessTokenRepository;
    }
    
    /**
     * 设置访问令牌存储。
     *
     * @param AccessTokenRepositoryInterface|string|array $value
     */
    public function setAccessTokenRepository($value)
    {
        if ($value instanceof AccessTokenRepositoryInterface) {
            $this->_accessTokenRepository = $value;
        } else {
            $this->_accessTokenRepository = Yii::createObject($value);
        }
        
        if (!$this->_accessTokenRepository instanceof AccessTokenRepositoryInterface) {
            throw new InvalidConfigException(get_class($this->_accessTokenRepository) . ' does not implement AccessTokenRepositoryInterface.');
        }
    }

    /**
     * 获取授权码存储。
     *
     * @return AuthCodeRepositoryInterface
     */
    public function getAuthCodeRepository()
    {
        return $this->_authCodeRepository;
    }
    
    /**
     * 设置授权码存储。
     *
     * @param AuthCodeRepositoryInterface|string|array $value
     */
    public function setAuthCodeRepository($value)
    {
        if ($value instanceof AuthCodeRepositoryInterface) {
            $this->_authCodeRepository = $value;
        } else {
            $this->_authCodeRepository = Yii::createObject($value);
        }
    
        if (!$this->_authCodeRepository instanceof AuthCodeRepositoryInterface) {
            throw new InvalidConfigException(get_class($this->_authCodeRepository) . ' does not implement AuthCodeRepositoryInterface.');
        }
    }
    
    /**
     * 获取客户端存储。
     * 
     * @return ClientRepositoryInterface
     */
    public function getClientRepository()
    {
        return $this->_clientRepository;
    }
    
    /**
     * 设置客户端存储。
     * 
     * @param ClientRepositoryInterface|string|array $value
     */
    public function setClientRepository($value)
    {
        if ($value instanceof ClientRepositoryInterface) {
            $this->_clientRepository = $value;
        } else {
            $this->_clientRepository = Yii::createObject($value);
        }
        
        if (!$this->_clientRepository instanceof ClientRepositoryInterface) {
            throw new InvalidConfigException(get_class($this->_clientRepository) . ' does not implement ClientRepositoryInterface.');
        }
    }

    /**
     * 获取更新令牌存储。
     *
     * @return RefreshTokenRepositoryInterface
     */
    public function getRefreshTokenRepository()
    {
        return $this->_refreshTokenRepository;
    }
    
    /**
     * 设置更新令牌存储。
     *
     * @param RefreshTokenRepositoryInterface|string|array $value
     */
    public function setRefreshTokenRepository($value)
    {
        if ($value instanceof RefreshTokenRepositoryInterface) {
            $this->_refreshTokenRepository = $value;
        } else {
            $this->_refreshTokenRepository = Yii::createObject($value);
        }
    
        if (!$this->_refreshTokenRepository instanceof RefreshTokenRepositoryInterface) {
            throw new InvalidConfigException(get_class($this->_refreshTokenRepository) . ' does not implement RefreshTokenRepositoryInterface.');
        }
    }
    
    /**
     * 获取权限存储。
     *
     * @return ScopeRepositoryInterface
     */
    public function getScopeRepository()
    {
        return $this->_scopeRepository;
    }
    
    /**
     * 设置权限存储。
     *
     * @param ScopeRepositoryInterface|string|array $value
     */
    public function setScopeRepository($value)
    {
        if ($value instanceof ScopeRepositoryInterface) {
            $this->_scopeRepository = $value;
        } else {
            $this->_scopeRepository = Yii::createObject($value);
        }
        
        if (!$this->_scopeRepository instanceof ScopeRepositoryInterface) {
            throw new InvalidConfigException(get_class($this->_scopeRepository) . ' does not implement ScopeRepositoryInterface.');
        }
    }
    
    /**
     * 获取用户存储。
     *
     * @return UserRepositoryInterface
     */
    public function getUserRepository()
    {
        return $this->_userRepository;
    }
    
    /**
     * 设置用户存储。
     *
     * @param UserRepositoryInterface|string|array $value
     */
    public function setUserRepository($value)
    {
        if ($value instanceof UserRepositoryInterface) {
            $this->_userRepository = $value;
        } else {
            $this->_userRepository = Yii::createObject($value);
        }
        
        if (!$this->_userRepository instanceof UserRepositoryInterface) {
            throw new InvalidConfigException(get_class($this->_userRepository) . ' does not implement UserRepositoryInterface.');
        }
    }
}