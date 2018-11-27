<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\web;

/**
 * BasicAsset 基础的 CSS 资源包，适用于  `Login`，`Register` 等页面。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class BasicAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';

    /**
     * {@inheritdoc}
     */
    public $css = [
        YII_ENV_DEV ? 'css/AdminLTE.css' : 'css/AdminLTE.min.css'
    ];
    
    /**
     * {@inheritdoc}
     */
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];
}
