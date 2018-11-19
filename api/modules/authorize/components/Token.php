<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiAuthorize\components;

use Yii;
use yii\helpers\ArrayHelper;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

/**
 * Token 生成或解析令牌的组件。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Token extends \yii\base\Component
{
    use JwtTrait;
    
    /**
     * @var string 客户端模式。
     */
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    
    /**
     * @var string 令牌加密 KEY。
     */
    public $signKey;
    
    /**
     * 生成客户端授权模式的令牌。
     * 
     * @param \api\models\Client $client 客户端模型。
     * @param array $options 令牌其它选项。
     * @return array
     */
    public function generateClientCredentials($client, $options = [])
    {
        $audience = ArrayHelper::getValue($options, 'audience', $client->name);
        $expiresIn = ArrayHelper::getValue($options, 'expiresIn', $client->token_expires_in);
        $issuer = ArrayHelper::getValue($options, 'issuer', Yii::$app->id);
        $subject = ArrayHelper::getValue($options, 'subject', self::GRANT_TYPE_CLIENT_CREDENTIALS);
        $issuedAt = ArrayHelper::getValue($options, 'issuedAt', time());
        
        /* @var $builder \Lcobucci\JWT\Builder */
        $builder = Yii::createObject(Builder::class);
        
        // 设置令牌参数。
        $builder->setIssuer($issuer)
            ->setAudience($audience)
            ->setSubject($subject)
            ->setIssuedAt($issuedAt)
            ->setExpiration($issuedAt + $expiresIn);
        
        // 设置自定义参数。
        $builder->set('client_id', $client->id);
        
        // 设置加密令牌。
        if ($this->signKey) {
            $signer = new Sha256();
            $builder->sign($signer, $this->signKey);
        }
        
        // 生成 JWT。
        $token = $builder->getToken();
        
        // 返回结果。
        return [
            'access_token' => (string) $token,
            'expires_in' => $expiresIn,
        ];
    }
    
    /**
     * 获取并返回客户端授权模式的令牌模型。
     * 
     * @param string $token 令牌。
     * @return \Lcobucci\JWT\Token 令牌模型。
     */
    public function getClientCredentials($token)
    {
        return static::loadJwt($token, $this->signKey);
    }
}