<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\controllers;

use Yii;
use yii\helpers\Html;

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
        $content[] = Html::a('授权演示', ['/demo/oauth'], ['target' => '_blank']);
        return implode(' | ', $content);
    }
}
