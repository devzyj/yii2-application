<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use common\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use common\oauth2\server\interfaces\AccessTokenEntityInterface;
use common\oauth2\server\interfaces\ClientRepositoryInterface;
use common\oauth2\server\interfaces\ClientEntityInterface;
use common\oauth2\server\interfaces\ScopeRepositoryInterface;
use common\oauth2\server\interfaces\ScopeEntityInterface;
use common\oauth2\server\interfaces\UserEntityInterface;
use common\oauth2\server\interfaces\RefreshTokenEntityInterface;
use common\oauth2\server\components\UniqueTokenIdentifierException;
use common\oauth2\server\components\JwtSignKey;

/**
 * Action class.
 * 
 * @property AccessTokenRepositoryInterface $accessTokenRepository 访问令牌存储。
 * @property ClientRepositoryInterface $clientRepository 客户端存储。
 * @property ScopeRepositoryInterface $scopeRepository 权限存储。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class Action extends \yii\base\Action
{
    /**
     * @var string 权限范围的分隔符。
     */
    const SCOPE_SEPARATOR = ' ';
    
    /**
     * @var integer 生成标识的最大次数。
     */
    const GENERATE_IDENDIFIER_MAX = 10;

    /**
     * @var JwtSignKey 生成令牌的私钥。
     */
    public $tokenPrivateKey;
    
    /**
     * @var AccessTokenRepositoryInterface 访问令牌存储。
     */
    private $_accessTokenRepository;
    
    /**
     * @var ClientRepositoryInterface 客户端存储。
     */
    private $_clientRepository;
    
    /**
     * @var ScopeRepositoryInterface 权限存储。
     */
    private $_scopeRepository;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if ($this->tokenPrivateKey === null) {
            throw new InvalidConfigException('The "tokenPrivateKey" property must be set.');
        } elseif ($this->_accessTokenRepository === null) {
            throw new InvalidConfigException('The "accessTokenRepository" property must be set.');
        } elseif ($this->_clientRepository === null) {
            throw new InvalidConfigException('The "clientRepository" property must be set.');
        } elseif ($this->_scopeRepository === null) {
            throw new InvalidConfigException('The "scopeRepository" property must be set.');
        }
    }
    
    /**
     * 获取授权类型。
     * 
     * @return string 返回值可以是 `authorization_code`, `password`, `client_credentials`, `refresh_token` 中的一个。
     */
    abstract public function getGrantType();
    
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
     * 从请求的头部，或者内容中获取客户端的认证信息。
     * 
     * 优先使用请求内容中的认证信息。
     * 
     * @param \yii\web\Request $request 请求对像。
     * @return array 认证信息。第一个元素为 `client_id`，第二个元素为 `client_secret`。
     * @throws \yii\web\BadRequestHttpException 缺少参数。
     */
    protected function getClientAuthCredentials($request)
    {
        // 从请求头中获取。
        list ($authUser, $authPassword) = $request->getAuthCredentials();
        
        // 从请求内容中获取。
        $identifier = $request->getBodyParam('client_id', $authUser);
        $secret = $request->getBodyParam('client_secret', $authPassword);
        
        if ($identifier === null || $secret === null) {
            throw new BadRequestHttpException('Missing parameters: "client_id" and "client_secret" required.');
        }
        
        return [$identifier, $secret];
    }
    
    /**
     * 获取客户端。
     * 
     * @param string $identifier 请求对像。
     * @return ClientEntityInterface 客户端。
     * @throws \yii\web\UnauthorizedHttpException 客户端不存在。
     */
    protected function getClient($identifier)
    {
        $entity = $this->getClientRepository()->getEntity($identifier);
        if (empty($entity)) {
            throw new UnauthorizedHttpException('Client authentication failed.');
        }
        
        return $entity;
    }
    
    /**
     * 验证客户端密钥。
     * 
     * @param ClientEntityInterface $client 客户端。
     * @param string $secret 需要验证的密钥。
     * @throws \yii\web\UnauthorizedHttpException 密钥不正确。
     */
    protected function validateClientSecret(ClientEntityInterface $client, $secret)
    {
        if ($client->getSecret() !== $secret) {
            throw new UnauthorizedHttpException('Client authentication failed.');
        }
    }
    
    /**
     * 验证客户端是否允许使用当前的授权类型。
     * 
     * @param ClientEntityInterface $client 客户端。
     * @throws \yii\web\ForbiddenHttpException 禁止的授权类型。
     */
    protected function validateClientGrantType(ClientEntityInterface $client)
    {
        if (!in_array($this->getGrantType(), $client->getGrantTypes())) {
            throw new ForbiddenHttpException('The grant type is unauthorized for this client.');
        }
    }

    /**
     * 获取请求中的权限。
     *
     * @param \yii\web\Request $request 请求对像。
     * @return ScopeEntityInterface[] 权限列表。
     */
    protected function getRequestedScopes($request)
    {
        $requestScope = $request->getBodyParam('scope');
        if ($requestScope === null || $requestScope === '') {
            return [];
        }
        
        // 转换成数组，并且过滤为空的权限。
        $requestScopes = array_filter(explode(self::SCOPE_SEPARATOR, $requestScope), function ($scope) {
            return $scope !== '';
        });
        
        // 循环验证权限是否有效。
        $result = [];
        foreach ($requestScopes as $scope) {
            if (!isset($result[$scope])) {
                $scopeEntity = $this->getScopeRepository()->getEntity($scope);
                if (empty($scopeEntity)) {
                    throw new BadRequestHttpException('The requested scope is invalid.');
                }
        
                $result[$scope] = $scopeEntity;
            }
        }
        
        return array_values($result);
    }
    
    /**
     * 生成并且保存访问令牌。
     * 
     * @param ScopeEntityInterface[] $scopes 需要关联的权限。
     * @param ClientEntityInterface $client 需要关联的客户端。
     * @param UserEntityInterface $user 需要关联的用户。
     * @return AccessTokenEntityInterface 生成并且保存成功的访问令牌。
     * @throws UniqueTokenIdentifierException 保存令牌时唯一标识重复。
     */
    protected function generateAccessToken(array $scopes, ClientEntityInterface $client, UserEntityInterface $user = null)
    {
        $token = $this->getAccessTokenRepository()->createEntity();
        
        // 添加权限。
        foreach ($scopes as $scope) {
            $token->addScope($scope);
        }
        
        // 设置客户端。
        $token->setClient($client);

        // 设置用户。
        if ($user) {
            $token->setUser($user);
        }

        // 设置过期时间。
        $token->setExpires(time() + $client->getAccessTokenDuration());
        
        // 生成唯一标识，并保存令牌。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成并设置唯一标识。
            $token->setIdentifier($this->generateUniqueIdentifier());
            
            try {
                // 保存令牌。
                $this->getAccessTokenRepository()->save($token);
                
                // 返回保存成功的令牌。
                return $token;
            } catch (UniqueTokenIdentifierException $e) {
                if ($count === 0) {
                    throw $e;
                }
            }
        }
    }

    /**
     * 生成唯一标识。
     * 
     * @param int $length 长度。
     * @return string 唯一标识。
     * @throws OAuthServerException
     */
    protected function generateUniqueIdentifier($length = 40)
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (\TypeError $e) {
            throw new ServerErrorHttpException('An unexpected error has occurred.');
        } catch (\Error $e) {
            throw new ServerErrorHttpException('An unexpected error has occurred.');
        } catch (\Exception $e) {
            throw new ServerErrorHttpException('Could not generate a random string.');
        }
    }
    
    /**
     * 生成认证信息。
     * 
     * @param ScopeEntityInterface[] $requestedScopes 请求的权限。
     * @param AccessTokenEntityInterface $accessToken 访问令牌。
     * @param RefreshTokenEntityInterface $refreshToken 更新令牌。
     * @return array
     * @todo 完善功能。
     */
    protected function generateCredentials($requestedScopes, $accessToken, $refreshToken = null)
    {
        //$client = $accessToken->getClient();
        //$user = $accessToken->getUser();
        //$tokenScopes = $accessToken->getScopes();
        
        $jwt = $accessToken->convertToJWT($this->tokenPrivateKey);
        
        $result = [
            'token_type' => 'Bearer',
            'access_token' => (string) $jwt,
            'expires_in' => $accessToken->getExpires() - time(),
            'scope' => null,
        ];
        
        /*if ($refreshToken instanceof RefreshTokenEntityInterface) {
            $result['refresh_token'] = '';
            $result['refresh_expires_in'] = '';
        }*/
        
        return $result;
    }
    
    /**
     * 验证回调地址。
     * 
     * @param ClientEntityInterface $client
     * @param string $redirectUri
     * @throws \yii\web\BadRequestHttpException
     
    protected function validateRedirectUri($client, $redirectUri)
    {
        
    }*/
}