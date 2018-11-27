<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\web;

/**
 * CustomMainAsset 自定义主要的 CSS 和 JS 资源包。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CustomMainAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@library/adminlte/assets/dist';

    /**
     * {@inheritdoc}
     */
    public $js = [
        'js/adminlte.js'
    ];
    
    /**
     * {@inheritdoc}
     */
    public $depends = [
        'library\adminlte\web\MainAsset',
        'library\adminlte\web\CustomBasicAsset',
    ];
}
