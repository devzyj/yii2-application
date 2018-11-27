<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\actions;

use Yii;

/**
 * ErrorAction 使用指定的视图显示应用程序错误信息。并且为游客使用特殊的布局或视图。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ErrorAction extends \yii\web\ErrorAction
{
    /**
     * @var string 游客使用的布局。如果为 `null` 则使用 [[$layout]]。
     */
    public $guestLayout = 'main-guest';
    
    /**
     * @var string 游客使用的视图。如果为 `null` 则使用 [[$view]]。
     */
    public $guestView;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (Yii::$app->getUser()->getIsGuest() && $this->guestLayout !== null) {
            $this->layout = $this->guestLayout;
        }
        
        return parent::run();
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHtmlResponse()
    {
        return $this->controller->render($this->getView(), $this->getViewRenderParams());
    }
    
    /**
     * 获取需要渲染的视图。
     *
     * @return string
     */
    protected function getView()
    {
        if (Yii::$app->getUser()->getIsGuest() && $this->guestView) {
            return $this->guestView;
        }
        
        return $this->view ?: $this->id;
    }
    
}