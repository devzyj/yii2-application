<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBin\components\tokens;

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
     * @var string 用于刷新的令牌类型。
     */
    const USAGE_TYPE_REFRESH = 'refresh';
    
    /**
     * @var string 令牌加密 KEY。
     */
    public $signKey;
    
    /**
     * @var \Lcobucci\JWT\Builder 构造器。
     */
    private $_builder;
    
    /**
     * 获取构造器。
     * 
     * @return \Lcobucci\JWT\Builder 构造器。
     */
    protected function getBuilder()
    {
        if ($this->_builder === null) {
            $this->_builder = Yii::createObject(Builder::class);
        }
        
        return $this->_builder;
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
     * 生成令牌。
     *
     * @param \Lcobucci\JWT\Builder $builder 构造器。
     * @param boolean $unsign 生成令牌后，是否移除加密内容。
     * @return \Lcobucci\JWT\Token $token 令牌模型。
     */
    protected function generateToken($builder, $unsign = false)
    {
        $this->signData($builder);
        $token = $builder->getToken();
        if ($unsign) {
            $builder->unsign();
        }
        
        return $token;
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
     * 生成访问令牌。
     *
     * @param \Lcobucci\JWT\Builder $builder 构造器。
     * @param boolean $unsign 生成令牌后，是否移除加密内容。
     * @return \Lcobucci\JWT\Token $token 令牌模型。
     */
    protected function generateAccessToken($builder, $unsign = false)
    {
        $this->setUsageType($builder, self::USAGE_TYPE_ACCESS);
        return $this->generateToken($builder, $unsign);
    }

    /**
     * 生成刷新令牌。
     *
     * @param \Lcobucci\JWT\Builder $builder 构造器。
     * @param boolean $unsign 生成令牌后，是否移除加密内容。
     * @return \Lcobucci\JWT\Token $token 令牌模型。
     */
    protected function generateRefreshToken($builder, $unsign = false)
    {
        $this->setUsageType($builder, self::USAGE_TYPE_REFRESH);
        return $this->generateToken($builder, $unsign);
    }
    
    /**
     * 生成客户端授权模式的令牌。
     * 
     * @param \api\models\Client $client 客户端模型。
     * @return array
     */
    public function generateClientCredentials($client)
    {
        // 获取构造器。
        $builder = $this->getBuilder();
        
        // 设置令牌参数。
        $builder->setIssuer(Yii::$app->id)
            ->setAudience($client->getPrimaryKey())
            ->set('client_id', $client->getPrimaryKey());
        
        // 设置令牌的生成时间。
        $issuedAt = time();
        $builder->setIssuedAt($issuedAt);
        
        // 设置访问令牌的过期时间。
        $builder->setExpiration($issuedAt + $client->token_expires_in);
        
        // 生成访问令牌。
        $accessToken = $this->generateAccessToken($builder);
        
        // 返回结果。
        return [
            'access_token' => (string) $accessToken,
            'expires_in' => $client->token_expires_in,
        ];
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
     * 获取并返回刷新令牌中的内容。
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