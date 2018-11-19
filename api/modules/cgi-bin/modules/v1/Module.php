<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBinV1;

use Yii;

/**
 * cgi-bin v1 接口模块。
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
    public function beforeAction($action)
    {
        // 设置身份认证类。
        Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'apiCgiBinV1\components\Identity',
            'enableSession' => false,
            'loginUrl' => null,
        ]);
        
        return parent::beforeAction($action);
    }
}