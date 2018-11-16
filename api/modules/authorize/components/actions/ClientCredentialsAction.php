<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiAuthorize\components\actions;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use api\models\Client;

/**
 * ClientCredentialsAction 实现了客户端模式的授权方式。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsAction extends \yii\base\Action
{
    /**
     * @var string 令牌加密 KEY。
     */
    public $signKey;
    
    /**
     * 生成令牌。
     * 
     * @throws BadRequestHttpException 缺少必要的参数。
     * @throws NotFoundHttpException 客户端不存在。
     * @throws ForbiddenHttpException 客户端不可用，或密钥错误。
     * @return array
     */
    public function run()
    {
        $params = Yii::$app->getRequest()->getBodyParams();
        if (!isset($params['client_id'])) {
            throw new BadRequestHttpException('Missing required parameters: client_id.');
        } elseif (!isset($params['client_secret'])) {
            throw new BadRequestHttpException('Missing required parameters: client_secret.');
        }
        
        /* @var $model Client */
        $model = Client::findOrSetOneById($params['client_id']);
        if (!$model) {
            throw new NotFoundHttpException('The `client_id` is invalid.');
        } elseif (!$model->getIsValid()) {
            throw new ForbiddenHttpException('The client is invalid.');
        } elseif ($params['client_secret'] !== $model->secret) {
            throw new ForbiddenHttpException('The `client_secret` is invalid.');
        }
        
        // 生成并返回令牌。
        return $this->generateToken($model);
    }
    
    /**
     * 生成并返回令牌。
     * 
     * @param Client $model
     * @return array
     */
    protected function generateToken($model)
    {
        $request = Yii::$app->getRequest();
        $grantType = 'client_credentials';
        $issued = time();
        $expiration = $issued + $model->token_expires_in;
        
        // JWT builder。
        /* @var $builder \Lcobucci\JWT\Builder */
        $builder = Yii::createObject(Builder::class);
        
        // 设置令牌参数。
        $builder->setIssuer($request->hostName)
            ->setAudience($model->name)
            ->setSubject($grantType)
            ->setIssuedAt($issued)
            ->setExpiration($expiration);
        
        // 设置自定义参数。
        $builder->set('grantType', $grantType)
            ->set('client_id', $model->id);
        
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
            'expires_in' => $model->token_expires_in,
        ];
    }
}