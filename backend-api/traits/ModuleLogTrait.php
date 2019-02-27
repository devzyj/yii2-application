<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\traits;

use Yii;
use yii\log\Dispatcher;
use yii\log\Logger;
use yii\log\FileTarget;
use backendApi\behaviors\LogResponseBehavior;

/**
 * ModuleLogTrait 提供了模块日志的相关方法，用于记录接口调用的日志。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait ModuleLogTrait
{
    /**
     * 获取模块日志组件。
     *
     * @return Dispatcher
     */
    public function getLog()
    {
        return $this->get('log');
    }
    
    /**
     * 设置模块日志组件。
     * 
     * 文件存放在：`@runtime/logs/modules/[MODULE_ID].log`
     */
    protected function setLog()
    {
        $this->set('log', [
            'class' => Dispatcher::class,
            'logger' => Logger::class,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'logFile' => "@runtime/logs/modules/{$this->getUniqueId()}.log",
                    'microtime' => true,
                    'logVars' => []
                ]
            ]
        ]);
    }
    
    /**
     * 附加记录响应时日志的行为。
     * 
     * @see LogResponseBehavior
     */
    protected function attachLogResponseBehavior()
    {
        Yii::$app->getResponse()->attachBehavior('logResponseBehavior', [
            'class' => LogResponseBehavior::class,
            'logger' => $this->getLog()->getLogger(),
        ]);
    }
}
