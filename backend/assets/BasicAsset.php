<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backend\assets;

/**
 * Basic application asset bundle.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class BasicAsset extends \yii\web\AssetBundle
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
        //'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',
        'css/basic.css',
        'font-googleapis/css/font-googleapis.css'
    ];

    /**
     * {@inheritdoc}
     */
    public $js = [
        'js/basic.js'
    ];

    /**
     * {@inheritdoc}
     */
    public $depends = [
        'devzyj\yii2\adminlte\web\BasicAdminLteAsset',
    ];
}
