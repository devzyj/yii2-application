<?php
/**
 * @link https://github.com/devzyj/yii2-admin
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
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
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return Yii::$app->getUniqueId() . ' index page.';
    }
}
