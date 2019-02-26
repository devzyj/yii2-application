<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\behaviors;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * LoadClientIdBehavior 通过判断客户端类型，为数据模型加载适当的 [[$attribute]] 的行为。
 * 
 * 依次判断下列条件，只会满足一条：
 * 如果客户端没有登录，属性将会被设置为 `null`。
 * 如果不是超级客户端，属性将会被设置为当前调用接口的客户端ID。
 * 如果是超级客户端，并且在 URL 中指定了 [[$clientIdParam]]，属性将会被设置为 URL 中指定的值。
 * 如果是超级客户端，并且没有指定属性的值，则属性将会被设置为当前调用接口的客户端ID。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class LoadClientIdBehavior extends \yii\base\Behavior
{
    /**
     * @var string 属性名称。
     */
    public $attribute = 'client_id';
    
    /**
     * @var string 参数名称。
     */
    public $clientIdParam = 'clientid';
    
    /**
     * @var boolean 是否只设置安全属性。
     */
    public $safeOnly = true;
    
    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            \devzyj\rest\Action::EVENT_AFTER_LOAD_MODEL => 'afterLoadModel',
        ];
    }
    
    /**
     * @param \devzyj\rest\ActionEvent $event
     * @see \devzyj\rest\Action::afterLoadModel()
     */
    public function afterLoadModel($event)
    {
        /* @var $model \yii\base\Model */
        $model = $event->object;
        
        /* @var $identity \backendApiRbacV1\components\ClientIdentity */
        if (!($user = Yii::$app->getUser()) || !($identity = $user->getIdentity(false)) || !($client = $identity->getRbacClient())) {
            // 客户端未登录，设置属性为 `null`。
            $this->setModelAttribute($model, $this->attribute, null);
            return;
        }
        
        if (!$client->getIsManager()) {
            // 如果不是管理类型的客户端，设置属性为当前调用接口的客户端ID。
            $this->setModelAttribute($model, $this->attribute, $client->getPrimaryKey());
            return;
        }
        
        // 如果是管理类型的客户端，并且在 URL 中指定了 [[$clientIdParam]]，则优先使用。
        // URL 中指定参数一般是在嵌套路由时使用。
        $clientId = Yii::$app->getRequest()->getQueryParam($this->clientIdParam);
        if ($clientId !== null) {
            // 设置属性为 URL 中指定的值。
            $this->setModelAttribute($model, $this->attribute, $clientId);
            return;
        }
        
        // 如果是管理类型的客户端，并且没有指定属性的值，则使用当前调用接口的客户端ID。
        $clientId = $this->getModelAttribute($model, $this->attribute);
        if ($clientId === null) {
            // 设置属性为当前调用接口的客户端ID。
            $this->setModelAttribute($model, $this->attribute, $client->getPrimaryKey());
            return;
        }
    }
    
    /**
     * 设置模型的属性值。
     * 
     * @param \yii\base\Model $model 模型。
     * @param string $attribute 属性名称。
     * @param mixed $value 属性值。
     */
    protected function setModelAttribute($model, $attribute, $value)
    {
        $model->setAttributes([$attribute => $value], $this->safeOnly);
    }
    
    /**
     * 
     * 获取模型的属性值。
     * 
     * @param \yii\base\Model $model 模型。
     * @param string $attribute 属性名称。
     * @return mixed 返回属性值。
     */
    protected function getModelAttribute($model, $attribute)
    {
        return ArrayHelper::getValue($model, $attribute);
    }
}
