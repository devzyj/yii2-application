<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiAuthorize;

use Yii;

/**
 * authorize 接口授权模块。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    /**
     * @var string 模块路由规则的配置文件。
     */
    public $urlRules = 'config/url-rules.php';

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        // 添加路由规则。
        $urlRules = require_once(Yii::getAlias($this->urlRules));
        /* @var $app \yii\web\Application */
        $app->getUrlManager()->addRules($urlRules);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // 设置令牌组件。
        $this->set('token', [
            'class' => 'apiAuthorize\components\JsonWebToken',
            'signKey' => Yii::$app->params['authorize.token.signKey'],
        ]);
        
        // 设置日志组件。
        $this->set('log', [
            'class' => 'yii\log\Dispatcher',
            'logger' => 'yii\log\Logger',
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => "@runtime/logs/modules/{$this->uniqueId}/app.log",
                    'maxLogFiles' => 50,
                    'microtime' => true,
                    'logVars' => [],
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            // 设置处理动作时记录日志的行为。
            'actionLogBehavior' => [
                'class' => 'api\components\behaviors\ActionLogBehavior',
                'logger' => $this->getLog()->getLogger(),
            ]
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // 设置响应行为。
        Yii::$app->getResponse()->attachBehaviors([
            // 设置响应时的日志行为。
            'responseLogBehavior' => [
                'class' => 'api\components\behaviors\ResponseLogBehavior',
                'logger' => $this->getLog()->getLogger(),
            ],
            // 设置是否始终使用 `200` 作为 HTTP 状态，并将实际的 HTTP 状态码包含在响应内容中。
            'suppressResponseCodeBehavior' => [
                'class' => 'devzyj\rest\behaviors\SuppressResponseCodeBehavior',
            ],
        ]);
        
        return parent::beforeAction($action);
    }
    
    /**
     * 获取令牌组件。
     * 
     * @return \apiAuthorize\components\JsonWebToken
     */
    public function getToken()
    {
        return $this->get('token');
    }

    /**
     * 获取日志组件。
     *
     * @return \yii\log\Dispatcher
     */
    public function getLog()
    {
        return $this->get('log');
    }
}