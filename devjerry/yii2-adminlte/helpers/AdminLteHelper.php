<?php
/**
 * @link https://github.com/devzyj/yii2-adminlte
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\adminlte\helpers;

use Yii;
use devjerry\yii2\adminlte\web\AdminLteAsset;

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
        /* @var $bundle AdminLteAsset */
        $bundle = Yii::$app->getAssetManager()->getBundle(AdminLteAsset::className());
        if (!$bundle->skin) {
            return '';
        } elseif ($bundle->skin != '_all-skins') {
            return $bundle->skin;
        }
        
        return $default;
    }
}
