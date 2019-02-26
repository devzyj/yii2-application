<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\web;

/**
 * CustomBasicAsset 自定义基础的 CSS 资源包。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CustomBasicAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@library/adminlte/assets/dist';

    /**
     * {@inheritdoc}
     */
    public $css = [
        'css/adminlte.css'
    ];
    
    /**
     * {@inheritdoc}
     */
    public $depends = [
        'library\adminlte\web\BasicAsset'
    ];
}
