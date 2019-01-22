<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiOauth\components;

use Yii;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

/**
 * JsonWebToken 生成或解析令牌的组件。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class JsonWebToken extends \yii\base\Component
{
    use JsonWebTokenTrait;
    
    /**
     * @var string 用于访问的令牌类型。
     */
    const USAGE_TYPE_ACCESS = 'access';

    /**
     * @var string 用于更新的令牌类型。
     */
    const USAGE_TYPE_REFRESH = 'refresh';
    
    /**
     * @var string 令牌加密 KEY。
     */
    public $signKey;

    /**
     * 生成用户认证信息。
     *
     * @param string $clientId 客户端ID。
     * @param string $userId 用户ID。
     * @param integer $expiresIn 访问令牌的过期时间。
     * @param integer $refreshExpiresIn 更新令牌的过期时间。
     * @param string $scope 作用域。
     * @return array 认证信息。
     * @return array
     */
    public function generateUserCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope = null)
    {
        return $this->generateCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope);
    }

    /**
     * 更新认证信息。
     *
     * @param string $clientId 客户端ID。
     * @param string $userId 用户ID。
     * @param integer $expiresIn 访问令牌的过期时间。
     * @param integer $refreshExpiresIn 更新令牌的过期时间。
     * @param string $scope 作用域。
     * @return array 认证信息。
     */
    public function refreshCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope = null)
    {
        return $this->generateCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope);
    }
    
    /**
     * 生成客户端认证信息。
     * 
     * @param string $clientId 客户端ID。
     * @param integer $expiresIn 访问令牌的过期时间。
     * @param integer $refreshExpiresIn 更新令牌的过期时间。
     * @param string $scope 作用域。
     * @return array 认证信息。
     */
    public function generateClientCredentials($clientId, $expiresIn, $refreshExpiresIn = null, $scope = null)
    {
        return $this->generateCredentials($clientId, null, $expiresIn, $refreshExpiresIn, $scope);
    }
    
    /**
     * 生成认证信息。
     * 
     * @param string $clientId 客户端ID。
     * @param string $userId 用户ID。
     * @param integer $expiresIn 访问令牌的过期时间。
     * @param integer $refreshExpiresIn 更新令牌的过期时间。
     * @param string $scope 作用域。
     * @return array 认证信息。
     */
    protected function generateCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope)
    {
        $builder = Yii::createObject(Builder::class);
        
        // 设置令牌参数。
        $now = time();
        $builder->setIssuedAt($now)
            ->setExpiration($now + $expiresIn)
            ->set('client_id', $clientId)
            ->set('user_id', $userId)
            ->set('scope', $scope);
        
        // 生成访问令牌。
        $accessToken = $this->generateAccessToken($builder);
        $token = [
            'access_token' => (string) $accessToken,
            'expires_in' => $expiresIn,
            'scope' => $scope,
        ];
        
        if ($refreshExpiresIn !== null) {
            $builder->unsign();
            
            // 设置更新令牌参数。
            $builder->setExpiration($now + $refreshExpiresIn);
            
            // 生成更新令牌。
            $refreshToken = $this->generateRefreshToken($builder);
            $token['refresh_token'] = (string) $refreshToken;
            $token['refresh_expires_in'] = $refreshExpiresIn;
        }
        
        return $token;
    }
    
    /**
     * 生成访问令牌。
     *
     * @param \Lcobucci\JWT\Builder $builder 构造器。
     * @return \Lcobucci\JWT\Token $token 令牌模型。
     */
    protected function generateAccessToken($builder)
    {
        $this->setUsageType($builder, self::USAGE_TYPE_ACCESS);
        return $this->generateToken($builder);
    }

    /**
     * 生成更新令牌。
     *
     * @param \Lcobucci\JWT\Builder $builder 构造器。
     * @return \Lcobucci\JWT\Token $token 令牌模型。
     */
    protected function generateRefreshToken($builder)
    {
        $this->setUsageType($builder, self::USAGE_TYPE_REFRESH);
        return $this->generateToken($builder);
    }
    
    /**
     * 生成令牌。
     *
     * @param \Lcobucci\JWT\Builder $builder 构造器。
     * @return \Lcobucci\JWT\Token $token 令牌模型。
     */
    protected function generateToken($builder)
    {
        $this->signData($builder);
        return $builder->getToken();
    }

    /**
     * 加密数据。
     *
     * @param \Lcobucci\JWT\Builder $builder 构造器。
     */
    protected function signData($builder)
    {
        if ($this->signKey) {
            $signer = new Sha256();
            $builder->sign($signer, $this->signKey);
        }
    }
    
    /**
     * 获取令牌的使用类型。
     * 
     * @param \Lcobucci\JWT\Token $token 令牌模型。
     * @return string 类型。
     */
    protected function getUsageType($token)
    {
        return $token->getClaim('usage_type', '');
    }
    
    /**
     * 设置令牌的使用类型。
     * 
     * @param \Lcobucci\JWT\Builder $builder 构造器。
     * @param string $type 类型。
     */
    protected function setUsageType($builder, $type)
    {
        $builder->set('usage_type', $type);
    }

    /**
     * 获取并返回令牌模型。
     *
     * @param string $token 令牌。
     * @return \Lcobucci\JWT\Token 令牌模型。
     */
    protected function getToken($token)
    {
        return static::loadJwt($token, $this->signKey);
    }
    
    /**
     * 获取并返回访问令牌中的内容。
     * 
     * @param string $token 令牌。
     * @return array 令牌内容。
     */
    public function getAccessTokenData($token)
    {
        $token = $this->getToken($token);
        if ($token && $this->getUsageType($token) === self::USAGE_TYPE_ACCESS) {
            return $token->getClaims();
        }
        
        return [];
    }
    
    /**
     * 获取并返回更新令牌中的内容。
     * 
     * @param string $token 令牌。
     * @return array 令牌内容。
     */
    public function getRefreshTokenData($token)
    {
        $token = $this->getToken($token);
        if ($token && $this->getUsageType($token) === self::USAGE_TYPE_REFRESH) {
            return $token->getClaims();
        }
        
        return [];
    }
}