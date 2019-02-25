<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\components;

use Yii;
use backendApi\filters\ClientIpFilterInterface;
use apiRbacV1\models\Client as RbacClient;

/**
 * 访问接口的客户端标识类。
 * 
 * @property \apiRbacV1\models\Client $rbacClient RBAC客户端模型。
 * @property boolean $isSuperRbacClient 是否超级RBAC客户端。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientIdentity extends \backendApi\components\ClientIdentity implements ClientIpFilterInterface
{
    /**
     * @var \apiRbacV1\models\Client RBAC客户端模型。
     */
    private $_rbacClient;
    
    /**
     * 获取RBAC客户端模型。
     * 
     * @return \apiRbacV1\models\Client
     */
    public function getRbacClient()
    {
        if ($this->_rbacClient === null) {
            $this->_rbacClient = RbacClient::findOne(['identifier' => $this->getId()]);
        }
        
        return $this->_rbacClient;
    }
    
    /******************************* IdentityInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var $model static */
        $model = parent::findIdentityByAccessToken($token, $type);
        if ($model && $model->getRbacClient()) {
            return $model;
        }
    }
    
    /******************************* RateLimitInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function getRateLimit($request, $action)
    {
        return $this->getRateLimitContents();
    }

    /******************************* ClientIpFilterInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function checkClientIp($ip, $action, $request)
    {
        return $this->checkClientAllowedIp($ip);
    }
}
