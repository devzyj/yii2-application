<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\actions;

use Yii;

/**
 * LogoutAction 用户登出的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class LogoutAction extends \yii\base\Action
{
    /**
     * 用户登出。
     */
    public function run()
    {
        Yii::$app->user->logout();
        return $this->controller->goHome();
    }
}