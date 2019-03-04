<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backend\controllers;

use Yii;

/**
 * Site controller.
 */
class SiteController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'devzyj\yii2\adminlte\actions\ErrorAction',
                //'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
            /*'login' => [
                'class' => 'library\adminlte\actions\LoginAction',
                'modelClass' => 'app\models\User',
            ],
            'logout' => [
                'class' => 'library\adminlte\actions\LogoutAction',
            ],*/
        ];
    }
    
    /**
     * 首页。
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * 用户登录。
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';
        $model = new \backend\models\user();
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
    /**
     * Displays profile page.
     *
     * @return string
     */
    public function actionProfile()
    {
        throw new \yii\web\NotFoundHttpException('没有找到页面。');
        //throw new \yii\web\ForbiddenHttpException('禁止的操作。');
        //throw new \yii\base\InvalidConfigException('无效的配置内容。');
        return $this->render('profile');
    }
    
    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
