<?php
/**
 * @link https://github.com/devzyj/yii2-adminlte
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\adminlte\web;

use yii\base\InvalidConfigException;

/**
 * AdminLteAsset class
 * 
 * 可以指定皮肤，使用方法如下：
 * ```php
 * [
 *     'components' => [
 *         'assetManager' => [
 *             'bundles' => [
 *                 'devjerry\yii2\adminlte\web\AdminLteAsset' => [
 *                     'skin' => 'skin-blue',
 *                 ]
 *             ]
 *         ]
 *     ]
 * ]
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AdminLteAsset extends \yii\web\AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';

    /**
     * {@inheritdoc}
     */
    public $js = [
        'js/adminlte.min.js'
    ];
    
    /**
     * {@inheritdoc}
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'devjerry\yii2\adminlte\web\BasicAsset',
    ];
    
    /**
     * @var string|false 选择皮肤。例如 `skin-blue` 或者设置 `false` 以禁用皮肤。
     * @see https://almsaeedstudio.com/themes/AdminLTE/documentation/index.html#layout
     */
    public $skin = '_all-skins';
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // 附加皮肤 CSS 文件。
        if ($this->skin) {
            if (('_all-skins' !== $this->skin) && (strpos($this->skin, 'skin-') !== 0)) {
                throw new InvalidConfigException('Invalid skin specified');
            }
            
            $this->css[] = sprintf(YII_ENV_DEV ? 'css/skins/%s.css' : 'css/skins/%s.min.css', $this->skin);
        }
        
        parent::init();
    }
}
