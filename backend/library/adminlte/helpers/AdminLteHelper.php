<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace library\adminlte\helpers;

use Yii;

/**
 * AdminLteHelper 助手类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AdminLteHelper
{
    /**
     * 获取使用的皮肤名称。
     * 
     * @param string $default 默认皮肤。
     * @return string
     */
    public static function skinClass($default = 'skin-blue')
    {
        $assetClass = 'library\adminlte\web\AdminLteAsset';
        
        /* @var $bundle \library\adminlte\web\AdminLteAsset */
        $bundle = Yii::$app->getAssetManager()->getBundle($assetClass);
        if ($bundle instanceof $assetClass) {
            if (!$bundle->skin) {
                return '';
            } elseif ($bundle->skin != '_all-skins') {
                return $bundle->skin;
            }
        }
        
        return $default;
    }
}
