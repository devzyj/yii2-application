<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use common\oauth2\server\repositories\AccessTokenRepositoryInterface;
use common\oauth2\server\entities\AccessTokenEntityInterface;
use common\oauth2\server\repositories\ClientRepositoryInterface;
use common\oauth2\server\entities\ClientEntityInterface;
use common\oauth2\server\repositories\ScopeRepositoryInterface;
use common\oauth2\server\entities\ScopeEntityInterface;
use common\oauth2\server\entities\UserEntityInterface;
use common\oauth2\server\exception\UniqueTokenIdentifierException;
use common\oauth2\server\entities\RefreshTokenEntityInterface;

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
class Action extends \yii\base\Action
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
     * 获取访问令牌存储。
     *
     * @return AccessTokenRepositoryInterface
     */
    public function getAccessTokenRepository()
    {
        if (!$this->_accessTokenRepository instanceof AccessTokenRepositoryInterface) {
            $this->_accessTokenRepository = Yii::createObject($this->_accessTokenRepository);
        }
    
        return $this->_accessTokenRepository;
    }
    
    /**
     * 设置访问令牌存储。
     *
     * @param AccessTokenRepositoryInterface|string|array $value
     */
    public function setAccessTokenRepository($value)
    {
        $this->_accessTokenRepository = $value;
    }
    
    /**
     * 获取客户端存储。
     * 
     * @return ClientRepositoryInterface
     */
    public function getClientRepository()
    {
        if (!$this->_clientRepository instanceof ClientRepositoryInterface) {
            $this->_clientRepository = Yii::createObject($this->_clientRepository);
        }
        
        return $this->_clientRepository;
    }
    
    /**
     * 设置客户端存储。
     * 
     * @param ClientRepositoryInterface|string|array $value
     */
    public function setClientRepository($value)
    {
        $this->_clientRepository = $value;
    }

    /**
     * 获取权限存储。
     *
     * @return ScopeRepositoryInterface
     */
    public function getScopeRepository()
    {
        if (!$this->_scopeRepository instanceof ScopeRepositoryInterface) {
            $this->_scopeRepository = Yii::createObject($this->_scopeRepository);
        }
    
        return $this->_scopeRepository;
    }
    
    /**
     * 设置权限存储。
     *
     * @param ScopeRepositoryInterface|string|array $value
     */
    public function setScopeRepository($value)
    {
        $this->_scopeRepository = $value;
    }
    
    /**
     * 从请求的头部，或者内容中获取 `client_id` 和 `client_secret`。
     *
     * @param \yii\web\Request $request 请求对像。
     * @return array 第一个元素为 `client_id`，第二个元素为 `client_secret`。
     * @throws \yii\web\BadRequestHttpException 缺少参数。
     */
    protected function getClientAuthCredentials($request)
    {
        list ($authUser, $authPassword) = $request->getAuthCredentials();
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
     * @throws \yii\base\InvalidConfigException 没有实现 [[ClientEntityInterface]]。
     */
    protected function getClient($identifier)
    {
        $entity = $this->getClientRepository()->getEntity($identifier);
        if (empty($entity)) {
            throw new UnauthorizedHttpException('Client authentication failed.');
        } elseif (!$entity instanceof ClientEntityInterface) {
            throw new InvalidConfigException(get_class($entity) . ' does not implement ClientEntityInterface.');
        }
        
        return $entity;
    }
    
    /**
     * 验证客户端密钥。
     * 
     * @param ClientEntityInterface $client 客户端。
     * @param string $secret 密钥。
     * @throws \yii\web\UnauthorizedHttpException 密钥不正确。
     */
    protected function validateClientSecret($client, $secret)
    {
        if ($client->getSecret() !== $secret) {
            throw new UnauthorizedHttpException('Client authentication failed.');
        }
    }
    
    /**
     * 验证客户端授权类型。
     * 
     * @param ClientEntityInterface $client 客户端。
     * @param string $grantType 授权类型。
     * @throws \yii\web\ForbiddenHttpException 禁止的授权类型。
     */
    protected function validateClientGrantType($client, $grantType)
    {
        if (!in_array($grantType, $client->getGrantTypes())) {
            throw new ForbiddenHttpException('The grant type is unauthorized for this client.');
        }
    }

    /**
     * 获取并且确认请求中的权限。
     *
     * @param \yii\web\Request $request 请求对像。
     * @return ScopeEntityInterface[] 权限列表。
     */
    protected function getScopes($request)
    {
        $requestScope = $request->getBodyParam('scope');
        if ($requestScope === null || $requestScope === '') {
            return [];
        }
        
        $scopes = array_filter(explode(self::SCOPE_SEPARATOR, $requestScope), function ($scope) {
            return $scope !== '';
        });
        
        return $this->ensureScopes($scopes);
    }
    
    /**
     * 确认并返回有效的权限列表。
     * 
     * @param string[] $scopes 权限标识列表。
     * @return ScopeEntityInterface[] 权限列表。
     * @throws \yii\web\BadRequestHttpException 权限不存在。
     * @throws \yii\base\InvalidConfigException 没有实现 [[ScopeEntityInterface]]。
     */
    protected function ensureScopes($scopes)
    {
        $result = [];
        foreach ($scopes as $scope) {
            $entity = $this->getScopeRepository()->getEntity($scope);
            if (empty($entity)) {
                throw new BadRequestHttpException('The requested scope is invalid.');
            } elseif (!$entity instanceof ScopeEntityInterface) {
                throw new InvalidConfigException(get_class($entity) . ' does not implement ScopeEntityInterface.');
            }
            
            $result[] = $entity;
        }
        
        return $result;
    }
    
    /**
     * 生成访问令牌。
     * 
     * @param integer $duration 持续时间。
     * @param ClientEntityInterface $client 客户端。
     * @param ScopeEntityInterface $scopes 权限。
     * @param UserEntityInterface $user 用户。
     * @return AccessTokenEntityInterface 访问令牌。
     * @throws \yii\base\InvalidConfigException 没有实现 [[AccessTokenEntityInterface]]。
     * @throws UniqueTokenIdentifierException 保存令牌时唯一标识重复。
     */
    protected function generateAccessToken($duration, $client, $scopes, $user = null)
    {
        $token = $this->getAccessTokenRepository()->createEntity();
        if (!$token instanceof AccessTokenEntityInterface) {
            throw new InvalidConfigException(get_class($token) . ' does not implement AccessTokenEntityInterface.');
        }
        
        $token->setExpires(time() + $duration);
        $token->setClient($client);
        $token->setScopes($scopes);
        $token->setUser($user);
        
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            $token->setIdentifier($this->generateUniqueIdentifier());
            
            try {
                $this->getAccessTokenRepository()->save($token);
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
     * @param ScopeEntityInterface[] $scopes 请求的权限。
     * @param AccessTokenEntityInterface $accessToken 访问令牌。
     * @param RefreshTokenEntityInterface $refreshToken 更新令牌。
     * @return array
     */
    protected function generateCredentials($scopes, $accessToken, $refreshToken = null)
    {
        $client = $accessToken->getClient();
        $user = $accessToken->getUser();
        $tokenScopes = $accessToken->getScopes();
        
        // TODO 
        $key = new Key($path);
        $jwt = $accessToken->convertToJWT($client->getPrivateKey());
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