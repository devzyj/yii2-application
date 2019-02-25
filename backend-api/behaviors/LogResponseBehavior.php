<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\behaviors;

use Yii;
use yii\web\Response;
use yii\log\Logger;
use yii\helpers\Json;

/**
 * LogResponseBehavior 实现了处理响应时记录日志的行为。
 * 
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'logResponseBehavior' => [
 *             'class' => 'backendApi\behaviors\LogResponseBehavior',
 *         ],
 *     ];
 * }
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class LogResponseBehavior extends \yii\base\Behavior
{
    /**
     * @var string 成功时记录日志。
     */
    const ITEMS_SUCCESSFUL = 'successful';

    /**
     * @var string 客户端错误时记录日志。
     */
    const ITEMS_CLIENT_ERROR = 'clientError';

    /**
     * @var string 服务器错误时记录日志。
     */
    const ITEMS_SERVER_ERROR = 'serverError';

    /**
     * @var string 其它状态时记录日志。
     */
    const ITEMS_OTHER = 'other';
    
    /**
     * @var \yii\log\Logger
     */
    public $logger;
    
    /**
     * @var array 需要记录日志的选项。
     */
    public $items = [
        self::ITEMS_SUCCESSFUL,
        self::ITEMS_CLIENT_ERROR,
        self::ITEMS_SERVER_ERROR,
        self::ITEMS_OTHER,
    ];
    
    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            Response::EVENT_BEFORE_SEND => 'beforeSend',
        ];
    }
    
    /**
     * 在发送响应前记录日志。
     * 
     * @param \yii\base\Event $event
     * @see \yii\web\Response::send()
     */
    public function beforeSend($event)
    {
        /* @var $response \yii\web\Response */
        $response = $event->sender;
        
        // 处理响应结果。
        if ($response->getIsSuccessful() && in_array(self::ITEMS_SUCCESSFUL, $this->items)) {
            $this->logSuccessful($event);
        } elseif ($response->getIsClientError() && in_array(self::ITEMS_CLIENT_ERROR, $this->items)) {
            $this->logClientError($event);
        } elseif ($response->getIsServerError() && in_array(self::ITEMS_SERVER_ERROR, $this->items)) {
            $this->logServerError($event);
        } elseif (in_array(self::ITEMS_OTHER, $this->items)) {
            $this->logOther($event);
        }
    }

    /**
     * @param \yii\base\Event $event
     */
    protected function logSuccessful($event)
    {
        $message = $this->getLogMessage($event);
        $this->logger->log(Json::encode($message), Logger::LEVEL_INFO, 'success');
    }

    /**
     * @param \yii\base\Event $event
     */
    protected function logClientError($event)
    {
        $message = $this->getLogMessage($event);
        $this->logger->log(Json::encode($message), Logger::LEVEL_ERROR, 'client');
    }

    /**
     * @param \yii\base\Event $event
     */
    protected function logServerError($event)
    {
        $message = $this->getLogMessage($event);
        $this->logger->log(Json::encode($message), Logger::LEVEL_ERROR, 'server');
    }

    /**
     * @param \yii\base\Event $event
     */
    protected function logOther($event)
    {
        $message = $this->getLogMessage($event);
        $this->logger->log(Json::encode($message), Logger::LEVEL_INFO, 'other');
    }

    /**
     * 获取日志信息。
     * 
     * @param \yii\base\Event $event
     */
    protected function getLogMessage($event)
    {
        /* @var $response \yii\web\Response */
        $response = $event->sender;
        $request = Yii::$app->getRequest();
        return [
            'action' => Yii::$app->controller->action->getUniqueId(),
            'statusCode' => $response->statusCode,
            'statusText' => $response->statusText,
            'data' => $response->data,
            'GET' => $request->getQueryParams(),
            'POST' => $request->getBodyParams(),
            'HEADERS' => $request->getHeaders(),
        ];
    }
}