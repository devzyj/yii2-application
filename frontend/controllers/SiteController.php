<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace frontend\controllers;

use Yii;

/**
 * Site controller.
 */
class SiteController extends \yii\web\Controller
{
    /**
     * Homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return Yii::$app->id . ' index page.';
    }
}
