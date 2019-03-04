<?php
/**
 * @link https://github.com/devzyj/yii2-adminlte
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use backend\assets\BasicAsset;

/* @var $this \yii\web\View */
/* @var $content string */

// 注册资源文件。
BasicAsset::register($this);
?>
<?php $this->beginContent('@devzyj/yii2/adminlte/views/layouts/main-guest.php'); ?>
<?= $content ?>
<?php $this->endContent(); ?>