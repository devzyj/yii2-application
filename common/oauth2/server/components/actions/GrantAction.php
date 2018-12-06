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
use yii\helpers\ArrayHelper;
use common\oauth2\server\interfaces\AccessTokenEntityInterface;
use common\oauth2\server\interfaces\ClientEntityInterface;
use common\oauth2\server\interfaces\RefreshTokenEntityInterface;
use common\oauth2\server\interfaces\ScopeEntityInterface;
use common\oauth2\server\interfaces\UserEntityInterface;
use common\oauth2\server\components\JwtSignKey;
use common\oauth2\server\components\UniqueTokenIdentifierException;

/**
 * GrantAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
abstract class GrantAction extends Action
{
    /**
     * @var string 授权码模式。
     */
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    
    /**
     * @var string 用户密码模式。
     */
    const GRANT_TYPE_PASSWORD = 'password';
    
    /**
     * @var string 客户端模式。
     */
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    
    /**
     * @var string 更新令牌模式。
     */
    const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';
    
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
     * @var ScopeEntityInterface[] 客户端请求的权限。
     */
    private $_requestedScopes;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->tokenPrivateKey === null) {
            throw new InvalidConfigException('The "tokenPrivateKey" property must be set.');
        } elseif ($this->accessTokenRepository === null) {
            throw new InvalidConfigException('The "accessTokenRepository" property must be set.');
        } elseif ($this->clientRepository === null) {
            throw new InvalidConfigException('The "clientRepository" property must be set.');
        } elseif ($this->scopeRepository === null) {
            throw new InvalidConfigException('The "scopeRepository" property must be set.');
        }
    }
    
    /**
     * 获取当前的授权模式。
     * 
     * @return string
     */
    abstract public function getGrantType();
    
    /**
     * 从请求的头部，或者内容中获取客户端的认证信息。
     * 优先使用请求内容中的认证信息。
     * 
     * @return array 认证信息。第一个元素为 `client_id`，第二个元素为 `client_secret`。
     * @throws \yii\web\BadRequestHttpException 缺少参数。
     */
    protected function getClientAuthCredentials()
    {
        // 从请求头中获取。
        list ($authUser, $authPassword) = $this->request->getAuthCredentials();
        
        // 从请求内容中获取。
        $identifier = $this->request->getBodyParam('client_id', $authUser);
        $secret = $this->request->getBodyParam('client_secret', $authPassword);
        
        if ($identifier === null || $secret === null) {
            throw new BadRequestHttpException('Missing parameters: "client_id" and "client_secret" required.');
        }
        
        return [$identifier, $secret];
    }
    
    /**
     * 使用客户端认证信息，获取客户端实例。
     * 
     * @param string $identifier 客户端标识。
     * @param string $secret 客户端密钥。
     * @return ClientEntityInterface 客户端。
     */
    protected function getClientByCredentials($identifier, $secret)
    {
        $client = $this->getClientRepository()->getClientEntityByCredentials($identifier, $secret);
        if (empty($client)) {
            throw new UnauthorizedHttpException('Client authentication failed.');
        } elseif (!$client instanceof ClientEntityInterface) {
            throw new InvalidConfigException(get_class($client) . ' does not implement ClientEntityInterface.');
        }
        
        return $client;
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
     * @return ScopeEntityInterface[] 权限列表。
     */
    protected function getRequestedScopes()
    {
        if ($this->_requestedScopes === null) {
            $this->_requestedScopes = [];
            $strScope = $this->request->getBodyParam('scope');
            if ($strScope !== null && $strScope !== '') {
                // 转换成数组，并且过滤为空的权限。
                $scopes = array_filter(explode(self::SCOPE_SEPARATOR, $strScope), function ($scope) {
                    return $scope !== '';
                });
                
                // 循环验证权限是否有效。
                $validScopes = [];
                foreach ($scopes as $scope) {
                    if (!isset($validScopes[$scope])) {
                        $scopeEntity = $this->getScopeRepository()->getScopeEntity($scope);
                        if (empty($scopeEntity)) {
                            throw new BadRequestHttpException('The requested scope is invalid.');
                        } elseif (!$scopeEntity instanceof ScopeEntityInterface) {
                            throw new InvalidConfigException(get_class($scopeEntity) . ' does not implement ScopeEntityInterface.');
                        }
            
                        $validScopes[$scope] = $scopeEntity;
                    }
                }
                
                $this->_requestedScopes = array_values($validScopes);
            }
        }
        
        return $this->_requestedScopes;
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
        $token = $this->getAccessTokenRepository()->createAccessTokenEntity();
        if (!$token instanceof AccessTokenEntityInterface) {
            throw new InvalidConfigException(get_class($token) . ' does not implement AccessTokenEntityInterface.');
        }
        
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
                $this->getAccessTokenRepository()->saveAccessToken($token);
                
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
     * 生成并且保存更新令牌。
     *
     * @param AccessTokenEntityInterface $accessToken 访问令牌。
     * @return RefreshTokenEntityInterface 生成并且保存成功的更新令牌。
     * @throws UniqueTokenIdentifierException 保存令牌时唯一标识重复。
     */
    protected function generateRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $token = $this->getRefreshTokenRepository()->createRefreshTokenEntity();
        if (!$token instanceof RefreshTokenEntityInterface ) {
            throw new InvalidConfigException(get_class($token) . ' does not implement RefreshTokenEntityInterface .');
        }
        
        // 设置关联的访问令牌。
        $token->setAccessToken($accessToken);
        
        // 设置过期时间。
        $client = $accessToken->getClient();
        $token->setExpires(time() + $client->getRefreshTokenDuration());

        // 生成唯一标识，并保存令牌。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成并设置唯一标识。
            $token->setIdentifier($this->generateUniqueIdentifier());
        
            try {
                // 保存令牌。
                $this->getRefreshTokenRepository()->saveRefreshToken($token);
        
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
     * @param AccessTokenEntityInterface $accessToken 访问令牌。
     * @param RefreshTokenEntityInterface $refreshToken 更新令牌。
     * @return array
     */
    protected function generateCredentials(AccessTokenEntityInterface $accessToken, RefreshTokenEntityInterface $refreshToken = null)
    {
        // 把访问令牌转换成 JWT。
        $accessJwt = $accessToken->convertToJWT($this->tokenPrivateKey);

        // 确认认证信息中要展示的权限。
        $scopes = $this->ensureCredentialsScopes($accessToken);
        if ($scopes) {
            $scopes = implode(self::SCOPE_SEPARATOR, $this->getIdentifierColumn($scopes));
        }
        
        // 访问令牌的信息。
        $result = [
            'token_type' => 'Bearer',
            'access_token' => (string) $accessJwt,
            'expires_in' => $accessToken->getExpires() - time(),
            'scope' => $scopes ? $scopes : null,
        ];
        
        // 更新令牌。
        if ($refreshToken) {
            $client = $accessToken->getClient();
            $user = $accessToken->getUser();
            $scopes = $this->getIdentifierColumn($accessToken->getScopes());
            $refreshTokenData = [
                'refresh_token_id' => $refreshToken->getIdentifier(),
                'access_token_id' => $accessToken->getIdentifier(),
                'client_id' => $client->getIdentifier(),
                'user_id' => $user ? $user->getIdentifier() : null,
                'scopes' => $scopes,
                'expires' => $refreshToken->getExpires(),
            ];
            
            // TODO 加密数据。
            $key = $client->getEncryptionKey();
            $result['refresh_token'] = $refreshTokenData;
            $result['refresh_expires_in'] = $refreshToken->getExpires() - time();
        }
        
        return $result;
    }
    
    /**
     * 确认认证信息中要展示的权限。
     * 
     * @param AccessTokenEntityInterface $accessToken 访问令牌。
     * @return ScopeEntityInterface[]
     */
    protected function ensureCredentialsScopes($accessToken)
    {
        // 确认认证信息中要展示的权限。
        $requestedScopes = $this->getRequestedScopes();
        if ($requestedScopes) {
            $tokenScopes = $accessToken->getScopes();
            if (count($requestedScopes) != count($tokenScopes)) {
                // 令牌中的权限和请求的权限数量不同时，在认证信息中显示令牌中的权限。
                return $tokenScopes;
            }
            
            // 令牌中的权限和请求的权限数量相同时，判断权限是否一致。
            // 获取请求的权限标识列表。
            $requestedScopeIds = $this->getIdentifierColumn($requestedScopes);
        
            // 获取令牌中的权限标识列表。
            $tokenScopeIds = $this->getIdentifierColumn($tokenScopes);
    
            // 判断权限是否一致。
            if (array_diff($requestedScopeIds, $tokenScopeIds)) {
                // 权限不一致时，在认证信息中显示令牌中的权限。
                return $tokenScopes;
            }
        }
        
        return [];
    }
    
    /**
     * 获取标识符列表。
     * 
     * @return array
     */
    protected function getIdentifierColumn(array $array)
    {
        return ArrayHelper::getColumn($array, function ($element) {
            return $element->getIdentifier();
        });
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