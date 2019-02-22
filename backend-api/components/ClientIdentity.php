<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\components;

use Yii;
use yii\web\IdentityInterface;
use yii\httpclient\Client;
use backendApi\models\OauthClient;
use yii\helpers\Json;

/**
 * 访问接口的客户端身份认证类。
 * 
 * @property boolean $isSuperClient 是否超级客户端。
 * @property boolean $clientIsValid 客户端是否有效，超级客户端始终有效。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientIdentity extends OauthClient implements IdentityInterface
{
    /**
     * 是否为超级客户端。
     * 
     * @return boolean
     */
    public function getIsSuperClient()
    {
        return in_array($this->getId(), Yii::$app->params['superClients']);
    }
    
    /**
     * 客户端是否有效。
     * 
     * @return boolean 是否有效，超级客户端始终有效。
     */
    public function getClientIsValid()
    {
        if ($this->getIsSuperClient()) {
            return true;
        }
        
        return $this->getIsValid();
    }

    /**
     * 检查  IP 是否被允许。
     *
     * @return boolean 是否允许，超级客户端始终允许。
     * @deprecated
     */
    public function checkClientAllowedIp($ip)
    {
        if ($this->getIsSuperClient()) {
            return true;
        }
        
        return $this->checkAllowedIp($ip);
    }

    /**
     * 检查 API 是否被允许。
     *
     * @return boolean 是否允许，超级客户端始终允许。
     * @deprecated
     */
    public function checkClientAllowedApi($api)
    {
        if ($this->getIsSuperClient()) {
            return true;
        }

        return $this->checkAllowedApi($api);
    }
    
    /******************************* IdentityInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var $oauthClient Client */
        $oauthClient = Yii::$app->get('oauthClient');
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}1",
        ];
        $response = $oauthClient->get('resource', null, $headers)->send();
        if ($response->getIsOk()) {
            $content = Json::decode($response->getContent());
            
            // TODO 
            
            var_dump($content);
        } else {
            $content = Json::decode($response->getContent());
            
            
            print_r($response);
        }
        exit();
        
        
        /* @var $module \backendApiOauth\Module */
        $module = Yii::$app->getModule('oauth');
        $tokenData = $module->getToken()->getAccessTokenData($token);
        if (isset($tokenData['client_id'])) {
            /* @var $model static */
            $model = static::findOneByIdentifier($tokenData['client_id']);
            if ($model && $model->getClientIsValid()) {
                return $model;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {}

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {}
}
