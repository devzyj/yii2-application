<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ClientEntityInterface;
use devjerry\oauth2\server\interfaces\RefreshTokenEntityInterface;
use devjerry\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\oauth2\server\interfaces\UserEntityInterface;
use devjerry\oauth2\server\exceptions\UniqueIdentifierException;

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
     * @var mixed 访问令牌密钥。
     */
    public $accessTokenCryptKey;

    /**
     * @var mixed 更新令牌密钥。
     */
    public $refreshTokenCryptKey;
    
    /**
     * @var ScopeEntityInterface[] 客户端请求的权限。
     */
    private $_requestedScopes;
    
    /**
     * 获取当前的授权模式。
     * 
     * @return string
     */
    abstract public function getGrantType();
    
    /**
     * 获取正在请求授权的客户端。
     * 
     * @return ClientEntityInterface 客户端。
     */
    protected function getAuthorizeClient()
    {
        // 获取客户端认证信息。
        list ($identifier, $secret) = $this->getClientAuthCredentials();
        
        // 获取并返回正在授权的客户端实例。
        return $this->getClientByCredentials($identifier, $secret);
    }
    
    /**
     * 从请求的头部，或者内容中获取客户端的认证信息。
     * 优先使用请求内容中的认证信息。
     * 
     * @return array 认证信息。第一个元素为 `client_id`，第二个元素为 `client_secret`。
     * @throws BadRequestHttpException 缺少参数。
     */
    protected function getClientAuthCredentials()
    {
        // 从请求头中获取。
        list ($authUser, $authPassword) = $this->request->getAuthCredentials();
        
        // 从请求内容中获取。
        $identifier = $this->request->getBodyParam('client_id', $authUser);
        $secret = $this->request->getBodyParam('client_secret', $authPassword);
        if ($identifier === null) {
            throw new BadRequestHttpException('Missing parameters: "client_id" required.');
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
        if (!$client instanceof ClientEntityInterface) {
            throw new UnauthorizedHttpException('Client authentication failed.');
        }
        
        return $client;
    }
    
    /**
     * 验证客户端是否允许使用当前的授权类型。
     * 
     * @param ClientEntityInterface $client 客户端。
     * @throws ForbiddenHttpException 禁止的授权类型。
     */
    protected function validateClientGrantType(ClientEntityInterface $client)
    {
        if (!in_array($this->getGrantType(), $client->getGrantTypes())) {
            throw new ForbiddenHttpException('The grant type is unauthorized for this client.');
        }
    }

    /**
     * 获取请求的权限。
     * 
     * @param string|string[] $default 默认权限。多个权限可以是数组，也可以是以 [[SELF::SCOPE_SEPARATOR]] 分隔的字符串。
     * @return ScopeEntityInterface[] 权限列表。
     */
    protected function getRequestedScopes($default = null)
    {
        $requestedScopes = $this->request->getBodyParam('scope', $default);
        if (!is_array($requestedScopes)) {
            $requestedScopes = array_filter(explode(self::SCOPE_SEPARATOR, trim($requestedScopes)), function ($scope) {
                return $scope !== '';
            });
        }
        
        return $this->validateScopes($requestedScopes);
    }
    
    /**
     * 验证权限。
     * 
     * @param string[] $scopes 需要验证的权限标识。
     * @return ScopeEntityInterface[] 验证有效的权限。
     */
    protected function validateScopes(array $scopes)
    {
        $validScopes = [];
        foreach ($scopes as $scope) {
            if (!isset($validScopes[$scope])) {
                $scopeEntity = $this->getScopeRepository()->getScopeEntity($scope);
                if (!$scopeEntity instanceof ScopeEntityInterface) {
                    throw new BadRequestHttpException('Scope is invalid.');
                }
        
                $validScopes[$scope] = $scopeEntity;
            }
        }
        
        return array_values($validScopes);
    }

    /**
     * 生成并且保存访问令牌。
     * 
     * @param ScopeEntityInterface[] $scopes 需要关联的权限。
     * @param ClientEntityInterface $client 需要关联的客户端。
     * @param UserEntityInterface $user 需要关联的用户。
     * @return AccessTokenEntityInterface 生成并且保存成功的访问令牌。
     * @throws UniqueIdentifierException 保存令牌时唯一标识重复。
     */
    protected function generateAccessToken(array $scopes, ClientEntityInterface $client, UserEntityInterface $user = null)
    {
        $accessToken = $this->getAccessTokenRepository()->createAccessTokenEntity();
        if (!$accessToken instanceof AccessTokenEntityInterface) {
            throw new InvalidConfigException(get_class($accessToken) . ' does not implement AccessTokenEntityInterface.');
        }

        // 添加权限。
        foreach ($scopes as $scope) {
            $accessToken->addScopeEntity($scope);
        }

        // 设置客户端。
        $accessToken->setClientEntity($client);
        
        // 设置用户。
        if ($user) {
            $accessToken->setUserEntity($user);
        }

        // 设置过期时间。
        $accessToken->setExpires(time() + $client->getAccessTokenDuration());
        
        // 生成唯一标识，并保存令牌。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成并设置唯一标识。
            $accessToken->setIdentifier($this->generateUniqueIdentifier());
            
            try {
                // 保存令牌。
                $this->getAccessTokenRepository()->saveAccessTokenEntity($accessToken);
                
                // 返回保存成功的令牌。
                return $accessToken;
            } catch (UniqueIdentifierException $e) {
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
     * @throws UniqueIdentifierException 保存令牌时唯一标识重复。
     */
    protected function generateRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $refreshToken = $this->getRefreshTokenRepository()->createRefreshTokenEntity();
        if (!$refreshToken instanceof RefreshTokenEntityInterface ) {
            throw new InvalidConfigException(get_class($refreshToken) . ' does not implement RefreshTokenEntityInterface.');
        }
        
        // 设置关联的访问令牌。
        $refreshToken->setAccessTokenEntity($accessToken);

        // 设置客户端。
        $client = $accessToken->getClientEntity();
        $refreshToken->setClientIdentifier($client->getIdentifier());

        // 设置用户。
        $user = $accessToken->getUserEntity();
        if ($user) {
            $refreshToken->setUserIdentifier($user->getIdentifier());
        }

        // 添加权限。
        $scopes = $accessToken->getScopeEntities();
        foreach ($scopes as $scope) {
            $refreshToken->addScopeIdentifier($scope->getIdentifier());
        }
        
        // 设置过期时间。
        $refreshToken->setExpires(time() + $client->getRefreshTokenDuration());

        // 生成唯一标识，并保存令牌。
        $count = self::GENERATE_IDENDIFIER_MAX;
        while ($count-- > 0) {
            // 生成并设置唯一标识。
            $refreshToken->setIdentifier($this->generateUniqueIdentifier());
        
            try {
                // 保存令牌。
                $this->getRefreshTokenRepository()->saveRefreshTokenEntity($refreshToken);
        
                // 返回保存成功的令牌。
                return $refreshToken;
            } catch (UniqueIdentifierException $e) {
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
     * @return array 认证信息。
     */
    protected function generateCredentials(AccessTokenEntityInterface $accessToken, RefreshTokenEntityInterface $refreshToken = null)
    {
        // 获取访问令牌中的权限标识列表。
        $scopeIdentifiers = ArrayHelper::getColumn($accessToken->getScopeEntities(), function ($element) {
            /* @var $element ScopeEntityInterface */
            return $element->getIdentifier();
        });

        // 认证信息中要展示的权限。
        $scope = null;
        if ($scopeIdentifiers) {
            $scope = implode(self::SCOPE_SEPARATOR, $scopeIdentifiers);
        }
        
        // 访问令牌信息。
        $credentials = [
            'token_type' => 'Bearer',
            'access_token' => $this->getAccessTokenRepository()->serializeAccessTokenEntity($accessToken, $this->accessTokenCryptKey),
            'expires_in' => $accessToken->getExpires() - time(),
            'scope' => $scope,
        ];
        
        // 更新令牌信息。
        if ($refreshToken) {
            $credentials['refresh_token'] = $this->getRefreshTokenRepository()->serializeRefreshTokenEntity($refreshToken, $this->refreshTokenCryptKey);
            $credentials['refresh_expires_in'] = $refreshToken->getExpires() - time();
        }
        
        // 返回认证信息。
        return $credentials;
    }
    
    /**
     * 验证回调地址。
     * 
     * @param ClientEntityInterface $client
     * @param string $redirectUri
     * @throws BadRequestHttpException
     
    protected function validateRedirectUri($client, $redirectUri)
    {
        
    }*/
}