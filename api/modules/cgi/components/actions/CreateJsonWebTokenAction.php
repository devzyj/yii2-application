<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgi\components\actions;

use Yii;
use yii\base\InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

/**
 * CreateJsonWebTokenAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CreateJsonWebTokenAction extends \yii\base\Action
{
    /**
     * @var string 令牌加密 KEY。
     */
    public $signKey;
    
    /**
     * @return array
     */
    public function run()
    {
        $user = Yii::$app->getUser();
        if (!$user || !($identity = $user->getIdentity(false))) {
            throw new InvalidArgumentException('User identity is null.');
        }
        
        // 生成并返回令牌。
        return $this->generateToken($identity);
    }
    
    /**
     * 生成并返回令牌。
     * 
     * @param \cgi\components\Identity $identity
     * @return array
     */
    protected function generateToken($identity)
    {
        $request = Yii::$app->getRequest();
        $issued = time();
        $duration = $identity->token_expires_in;
        $expiration = $issued + $duration;
        
        // JWT builder。
        /* @var $builder \Lcobucci\JWT\Builder */
        $builder = Yii::createObject(Builder::class);
        
        // 设置令牌参数。
        $builder->setIssuer($request->hostInfo)
            ->setAudience($identity->name)
            ->setIssuedAt($issued)
            ->setExpiration($expiration);
        
        // 设置自定义参数。
        $builder->set('identifier', $identity->identifier);
        
        // 设置加密令牌。
        if ($this->signKey !== null) {
            $signer = new Sha256();
            $builder->sign($signer, $this->signKey);
        }
        
        // 生成 JWT。
        $token = $builder->getToken();
        
        // 返回结果。
        return [
            'access_token' => (string) $token,
            'expires_in' => $duration,
        ];
    }
}