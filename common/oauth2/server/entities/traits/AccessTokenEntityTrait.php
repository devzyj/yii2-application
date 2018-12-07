<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities\traits;

use Yii;
use yii\helpers\ArrayHelper;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HmacSha256;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Signer\Key;
use common\oauth2\server\CryptKey;

/**
 * AccessTokenEntityTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait AccessTokenEntityTrait
{
    use TokenEntityTrait;
    
    /**
     * 转换成 JWT。
     *
     * @param CryptKey $key
     * @return string
     
    public function convertToJWT(CryptKey $key)
    {
        $scopes = ArrayHelper::getColumn($this->getScopes(), function ($element) {
            return $element->getIdentifier();
        });
        
        $builder = new Builder();
        $builder->setId($this->getIdentifier())
            ->setAudience($this->getClient()->getIdentifier())
            ->setSubject($this->getUser() ? $this->getUser()->getIdentifier() : null)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->getExpires())
            ->set('scopes', $scopes);
        
        if ($key) {
            if ($key->isSecretKey()) {
                $builder->sign(new HmacSha256(), $key->getKey());
            } elseif ($key->isPrivateKey()) {
                $builder->sign(new RsaSha256(), new Key('file://' . $key->getKey(), $key->getPassphrase()));
            }
        }
        
        return $builder->getToken();
    }*/
}