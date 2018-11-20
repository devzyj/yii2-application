<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components\behaviors;

use Yii;
use yii\base\Module;
use yii\log\Logger;
use yii\helpers\Json;

/**
 * ActionLogBehavior 实现了在处理动作时记录日志的行为。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ActionLogBehavior extends \yii\base\Behavior
{
    /**
     * @var \yii\log\Logger
     */
    public $logger;
    
    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            Module::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }
    
    /**
     * @param \yii\base\ActionEvent $event
     * @see \yii\base\Module::beforeAction()
     * @see \yii\base\Controller::beforeAction()
     */
    public function beforeAction($event)
    {
        $request = Yii::$app->getRequest();
        $controller = Yii::$app->controller;
        $message = [
            'action' => $event->action->getUniqueId(),
            'GET' => $request->getQueryParams(),
            'POST' => $request->getBodyParams(),
            'HEADERS' => $request->getHeaders(),
        ];

        $this->logger->log(Json::encode($message), Logger::LEVEL_INFO, 'call');
    }
}