<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\web;

/**
 * FontAsset 字体资源包。
 * 
 * @link https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class FontAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@library/adminlte/assets/font-googleapis';
    
    /**
     * {@inheritdoc}
     */
    public $css = [
        'css/font-googleapis.css'
    ];
}
