<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\behaviors;

use Yii;

/**
 * QueryClientIdBehavior 通过判断客户端类型，为查询对像添加 [[$attribute]] 过滤条件的行为。
 * 
 * 如果客户端没有登录，则增加过滤条件 `0=1`。
 * 如果不是超级客户端，则增加过滤条件为当前调用接口的客户端ID。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class QueryClientIdBehavior extends \yii\base\Behavior
{
    /**
     * @var string 属性名称。
     */
    public $attribute = 'client_id';

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            \devzyj\rest\Action::EVENT_AFTER_PREPARE_DATA_PROVIDER => 'afterPrepareDataProvider',
        ];
    }
    
    /**
     * @param \devzyj\rest\ActionEvent $event
     * @see \devzyj\rest\Action::afterPrepareDataProvider()
     */
    public function afterPrepareDataProvider($event)
    {
        /* @var $query \yii\db\ActiveQuery */
        $query = $event->object->query;

        // 应用程序的登录用户。
        $user = Yii::$app->getUser();
        
        /* @var $identity \backendApiRbacV1\components\ClientIdentity */
        if (!($user = Yii::$app->getUser()) || !($identity = $user->getIdentity(false)) || !($client = $identity->getRbacClient())) {
            // 用户未登录，设置过滤条件 `0=1`。
            $query->andWhere('0=1');
        } elseif (!$client->getIsManager()) {
            // 如果不是管理类型的客户端，设置过滤条件为当前调用接口的客户端ID。
            $query->andWhere($this->attribute, $client->getPrimaryKey());
        }
    }
}
