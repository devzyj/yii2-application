<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components\traits;

use Yii;

/**
 * ModuleLogTrait 提供了模块日志的相关方法，用于记录接口调用的日志。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait ModuleLogTrait
{
    /**
     * 获取日志组件。
     *
     * @return \yii\log\Dispatcher
     */
    public function getLog()
    {
        return $this->get('log');
    }
    
    /**
     * 设置日志组件。
     * 
     * 文件存放在：`@runtime/logs/modules/app.log`
     */
    protected function setLog()
    {
        $this->set('log', [
            'class' => 'yii\log\Dispatcher',
            'logger' => 'yii\log\Logger',
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => "@runtime/logs/modules/app.log",
                    'microtime' => true,
                    'logVars' => []
                ]
            ]
        ]);
    }
    
    /**
     * 附加记录响应时日志的行为。
     */
    protected function attachLogResponseBehavior()
    {
        Yii::$app->getResponse()->attachBehavior('logResponseBehavior', [
            'class' => 'api\components\behaviors\LogResponseBehavior',
            'logger' => $this->getLog()->getLogger(),
        ]);
    }
}
