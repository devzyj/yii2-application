<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backend\assets;

/**
 * Main application asset bundle.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AppAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $basePath = '@webroot/static';

    /**
     * {@inheritdoc}
     */
    public $baseUrl = '@web/static';

    /**
     * {@inheritdoc}
     */
    public $css = [
        'css/site.css',
        //'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic'
    ];

    /**
     * {@inheritdoc}
     */
    public $js = [
        'js/site.js'
    ];

    /**
     * {@inheritdoc}
     */
    public $depends = [
        'devjerry\yii2\adminlte\web\AdminLteAsset',
        'yii\web\YiiAsset',
        //'library\adminlte\web\FontsAsset',
        //'yii\bootstrap\BootstrapAsset'
    ];
}
