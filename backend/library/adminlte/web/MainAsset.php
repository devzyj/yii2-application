<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\web;

/**
 * MainAsset 主要的 CSS 和 JS 资源包，适用于登录后的页面。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class MainAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';

    /**
     * {@inheritdoc}
     */
    public $js = [
        YII_ENV_DEV ? 'js/adminlte.js' : 'js/adminlte.min.js'
    ];
    
    /**
     * {@inheritdoc}
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'library\adminlte\web\BasicAsset',
    ];
}
