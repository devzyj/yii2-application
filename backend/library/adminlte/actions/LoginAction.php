<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\actions;

use Yii;

/**
 * LoginAction 用户登录的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class LoginAction extends \yii\base\Action
{
    /**
     * @var string 登录模型类名。
     */
    public $modelClass;

    /**
     * @var string 视图名称。如果没有设置，它将取 [[$id]] 的值。
     */
    public $view;
    
    /**
     * @var string|false|null 动作使用的布局。
     */
    public $layout = 'base';
    
    /**
     * 用户登录。
     */
    public function run()
    {
        if (!Yii::$app->getUser()->isGuest) {
            return $this->goHome();
        }
        
        /* @var $model \yii\base\Model */
        $model = Yii::createObject($this->modelClass);
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        }

        // 设置使用的布局。
        if ($this->layout !== null) {
            $this->controller->layout = $this->layout;
        }
        
        // 渲染页面。
        return $this->controller->render($this->view ?: $this->id, [
            'model' => $model,
        ]);
    }
}