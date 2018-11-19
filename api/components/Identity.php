<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components;

use Yii;
use yii\web\IdentityInterface;

/**
 * 访问接口的客户端身份标识类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Identity extends \api\models\Client implements IdentityInterface
{
    /**
     * 是否为超级客户端。
     * 
     * 超级客户端不检查 `状态`、`IP`、`权限`。
     *
     * @return boolean
     */
    public function getIsSuperClient()
    {
        return in_array($this->getId(), Yii::$app->params['superClients']);
    }
    
    /**
     * 检查客户端状态是否有效。
     * 
     * @return boolean 是否有效，超级客户端始终有效。
     */
    public function checkClientStatus()
    {
        if ($this->getIsSuperClient()) {
            return true;
        }
        
        return $this->getIsValid();
    }

    /**
     * 检查客户端 IP 是否被允许访问。
     *
     * @return boolean 是否允许，超级客户端始终允许。
     */
    public function checkClientIPs($ip)
    {
        if ($this->getIsSuperClient()) {
            return true;
        }
        
        return $this->checkAllowedIPs($ip);
    }

    /**
     * 检查客户端是否允许访问 API。
     *
     * @return boolean 是否允许，超级客户端始终允许。
     */
    public function checkClientAPIs($api)
    {
        if ($this->getIsSuperClient()) {
            return true;
        }

        return $this->checkAllowedAPIs($api);
    }
    
    /******************************* IdentityInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->primaryKey;
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
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }
}
