<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\components;

use yii\helpers\ArrayHelper;
use backendApi\models\RbacClient;

/**
 * 访问接口的客户端标识类。
 * 
 * @property RbacClient|false $rbacClient RBAC 客户端模型。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientIdentity extends \backendApi\components\ClientIdentity
{
    /**
     * @var RbacClient|false RBAC 客户端模型。
     */
    private $_rbacClient = false;
    
    /**
     * 获取 RBAC 客户端模型。
     * 
     * @return RbacClient
     */
    public function getRbacClient()
    {
        if ($this->_rbacClient === false) {
            $this->_rbacClient = RbacClient::findOne(['identifier' => $this->getPrimaryKey()]);
        }
        
        return $this->_rbacClient;
    }
    
    /**
     * 检查是否允许访问模型。
     * 
     * @param object $model 需要检查的模型。
     * @return boolean 是否允许。
     */
    public function checkAllowedModel($model)
    {
        $rbacClient = $this->getRbacClient();
        if ($rbacClient) {
            if ($rbacClient->getIsManager()) {
                // 管理类型的客户端，始终允许访问模型。
                return true;
            } elseif ($rbacClient->getIsNormal()) {
                // 普通类型的客户端，检查是否允许访问模型。
                if ($model instanceof RbacClient) {
                    // 如果模型是 RBAC 客户端，检查模型主键是否等于调用接口的 RBAC 客户端主键。
                    return $model->getPrimaryKey() === $rbacClient->getPrimaryKey();
                }
                
                // 如果模型不是 RBAC 客户端，检查模型中的 `client_id` 属性值是否等于调用接口的 RBAC 客户端主键。
                $clientId = ArrayHelper::getValue($model, 'client_id');
                return $clientId === $rbacClient->getPrimaryKey();
            }
        }
        
        return false;
    }
    
    /******************************* IdentityInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var $model static */
        $model = parent::findIdentityByAccessToken($token, $type);
        
        // 验证 RBAC 客户端是否有效。
        if ($model && $model->getRbacClient()) {
            return $model;
        }
    }
}
