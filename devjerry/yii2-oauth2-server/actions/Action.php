<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use devzyj\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use devzyj\oauth2\server\interfaces\AuthorizationCodeRepositoryInterface;
use devzyj\oauth2\server\interfaces\ClientRepositoryInterface;
use devzyj\oauth2\server\interfaces\RefreshTokenRepositoryInterface;
use devzyj\oauth2\server\interfaces\ScopeRepositoryInterface;
use devzyj\oauth2\server\interfaces\UserRepositoryInterface;
use devjerry\yii2\oauth2\server\repositories\AccessTokenRepository;
use devjerry\yii2\oauth2\server\repositories\AuthorizationCodeRepository;
use devjerry\yii2\oauth2\server\repositories\ClientRepository;
use devjerry\yii2\oauth2\server\repositories\RefreshTokenRepository;
use devjerry\yii2\oauth2\server\repositories\ScopeRepository;
use devjerry\yii2\oauth2\server\repositories\UserRepository;

/**
 * Action class.
 * 
 * @property AccessTokenRepositoryInterface $accessTokenRepository 访问令牌存储库。
 * @property AuthorizationCodeRepositoryInterface $authCodeRepository 授权码存储库。
 * @property ClientRepositoryInterface $clientRepository 客户端存储库。
 * @property RefreshTokenRepositoryInterface $refreshTokenRepository 更新令牌存储库。
 * @property ScopeRepositoryInterface $scopeRepository 权限存储库。
 * @property UserRepositoryInterface $userRepository 用户存储库。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Action extends \yii\base\Action
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
     * @var AccessTokenRepositoryInterface 访问令牌存储库。
     */
    private $_accessTokenRepository;

    /**
     * @var AuthorizationCodeRepositoryInterface 授权码存储库。
     */
    private $_authorizationCodeRepository;
    
    /**
     * @var ClientRepositoryInterface 客户端存储库。
     */
    private $_clientRepository;

    /**
     * @var RefreshTokenRepositoryInterface 更新令牌存储库。
     */
    private $_refreshTokenRepository;
    
    /**
     * @var ScopeRepositoryInterface 权限存储库。
     */
    private $_scopeRepository;

    /**
     * @var UserRepositoryInterface 用户存储库。
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
     * 获取访问令牌存储库。
     *
     * @return AccessTokenRepositoryInterface
     */
    public function getAccessTokenRepository()
    {
        if ($this->_accessTokenRepository === null) {
            $this->_accessTokenRepository = Yii::createObject(AccessTokenRepository::class);
            if (!$this->_accessTokenRepository instanceof AccessTokenRepositoryInterface) {
                throw new InvalidConfigException(get_class($this->_accessTokenRepository) . ' does not implement AccessTokenRepositoryInterface.');
            }
        }
        
        return $this->_accessTokenRepository;
    }

    /**
     * 获取授权码存储库。
     *
     * @return AuthorizationCodeRepositoryInterface
     */
    public function getAuthorizationCodeRepository()
    {
        if ($this->_authorizationCodeRepository === null) {
            $this->_authorizationCodeRepository = Yii::createObject(AuthorizationCodeRepository::class);
            if (!$this->_authorizationCodeRepository instanceof AuthorizationCodeRepositoryInterface) {
                throw new InvalidConfigException(get_class($this->_authorizationCodeRepository) . ' does not implement AuthorizationCodeRepositoryInterface.');
            }
        }
        
        return $this->_authorizationCodeRepository;
    }
    
    /**
     * 获取客户端存储库。
     * 
     * @return ClientRepositoryInterface
     */
    public function getClientRepository()
    {
        if ($this->_clientRepository === null) {
            $this->_clientRepository = Yii::createObject(ClientRepository::class);
            if (!$this->_clientRepository instanceof ClientRepositoryInterface) {
                throw new InvalidConfigException(get_class($this->_clientRepository) . ' does not implement ClientRepositoryInterface.');
            }
        }
        
        return $this->_clientRepository;
    }

    /**
     * 获取更新令牌存储库。
     *
     * @return RefreshTokenRepositoryInterface
     */
    public function getRefreshTokenRepository()
    {
        if ($this->_refreshTokenRepository === null) {
            $this->_refreshTokenRepository = Yii::createObject(RefreshTokenRepository::class);
            if (!$this->_refreshTokenRepository instanceof RefreshTokenRepositoryInterface) {
                throw new InvalidConfigException(get_class($this->_refreshTokenRepository) . ' does not implement RefreshTokenRepositoryInterface.');
            }
        }
        
        return $this->_refreshTokenRepository;
    }
    
    /**
     * 获取权限存储库。
     *
     * @return ScopeRepositoryInterface
     */
    public function getScopeRepository()
    {
        if ($this->_scopeRepository === null) {
            $this->_scopeRepository = Yii::createObject(ScopeRepository::class);
            if (!$this->_scopeRepository instanceof ScopeRepositoryInterface) {
                throw new InvalidConfigException(get_class($this->_scopeRepository) . ' does not implement ScopeRepositoryInterface.');
            }
        }
        
        return $this->_scopeRepository;
    }
    
    /**
     * 获取用户存储库。
     *
     * @return UserRepositoryInterface
     */
    public function getUserRepository()
    {
        if ($this->_userRepository === null) {
            $this->_userRepository = Yii::createObject(UserRepository::class);
            if (!$this->_userRepository instanceof UserRepositoryInterface) {
                throw new InvalidConfigException(get_class($this->_userRepository) . ' does not implement UserRepositoryInterface.');
            }
        }
        
        return $this->_userRepository;
    }
}