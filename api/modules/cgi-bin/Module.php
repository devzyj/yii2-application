<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBin;

use Yii;
use api\components\traits\ModuleLogTrait;

/**
 * cgi-bin 接口模块。
 * 
 * @property \yii\log\Dispatcher $log 日志组件。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    use ModuleLogTrait;
    
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
        
        // 设置日志组件。
        $this->setLog();

        // 设置令牌组件。
        $this->set('token', [
            'class' => 'apiCgiBin\components\tokens\JsonWebToken',
            'signKey' => Yii::$app->params['token.signKey'],
        ]);
    }
    
    /**
     * 获取令牌组件。
     * 
     * @return \apiCgiBin\components\tokens\JsonWebToken
     */
    public function getToken()
    {
        return $this->get('token');
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // 设置身份认证类。
        Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'apiCgiBin\components\Identity',
            'enableSession' => false,
            'loginUrl' => null,
        ]);
        
        // 附加记录响应时日志的行为。
        $this->attachLogResponseBehavior();
        
        return parent::beforeAction($action);
    }
}