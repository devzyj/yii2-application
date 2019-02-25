<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\components;

use Yii;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use backendApi\models\OauthClient;
use backendApi\models\OauthClientSetting;
use backendApi\models\OauthScope;

/**
 * 访问接口的客户端身份认证类。
 * 
 * @property OauthClientSetting $oauthClientSetting 客户端配置
 * 
 * @property boolean $isSuper 是否超级客户端。
 * @property boolean $isValid 客户端是否有效，超级客户端始终有效。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientIdentity extends OauthClient implements IdentityInterface
{
    /**
     * @var array 访问令牌数据。
     */
    public $accessTokenData;
    
    /**
     * 是否为超级客户端。
     * 
     * @return boolean
     */
    public function getIsSuper()
    {
        return in_array($this->identifier, Yii::$app->params['superClients']);
    }
    
    /**
     * 客户端是否有效。
     * 
     * @return boolean 是否有效，超级客户端始终有效。
     */
    public function getIsValid()
    {
        if ($this->getIsSuper()) {
            return true;
        }
        
        return parent::getIsValid();
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean 是否允许，超级客户端始终允许。
     */
    public function checkAllowedIp($ip)
    {
        if ($this->getIsSuper()) {
            return true;
        }
        
        return parent::checkAllowedIp($ip);
    }
    
    /**
     * 检查是否允许访问接口。
     * 
     * @param string $api 需要检查的接口名称。
     * @return boolean 是否允许，超级客户端始终允许。
     */
    public function checkAllowedApi($api)
    {
        if ($this->getIsSuper()) {
            return true;
        }
        
        $tokenScopes = ArrayHelper::getValue($this->accessTokenData, 'scopes');
        if ($tokenScopes) {
            $scopes = $this->getOauthScopes()->andWhere(['identifier' => $tokenScopes])->all();
            foreach ($scopes as $scope) {
                /* @var $scope OauthScope */
                if ($scope->checkAllowedApi($api)) {
                    return true;
                }
            }
        }
        
        return false;
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
    {}

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var $module \devzyj\yii2\oauth2\server\Module */
        $module = Yii::$app->getModule('oauth2');
        $tokenData = $module->validateAccessToken($token);
        if (isset($tokenData['client_id'])) {
            /* @var $model static */
            $model = static::findOneByIdentifier($tokenData['client_id']);
            if ($model && $model->getIsValid()) {
                $model->accessTokenData = $tokenData;
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
