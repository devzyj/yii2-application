<?php
/**
 * @link https://github.com/devzyj/yii2-adminlte
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\adminlte\web;

/**
 * BasicAdminLteAsset 基础的资源包，适用于未登录的页面。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class BasicAdminLteAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';

    /**
     * {@inheritdoc}
     */
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // CSS 文件。
        if (empty($this->css)) {
            $this->css[] = YII_ENV_DEV ? 'css/AdminLTE.css' : 'css/AdminLTE.min.css';
        }
    
        parent::init();
    }
}
