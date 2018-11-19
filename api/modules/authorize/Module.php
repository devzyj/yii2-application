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
            'class' => 'apiAuthorize\components\Token',
            'signKey' => Yii::$app->params['authorize.token.signKey'],
        ]);
    }
    
    /**
     * 获取令牌组件。
     * 
     * @return \apiAuthorize\components\Token
     */
    public function getToken()
    {
        return $this->get('token');
    }
}