<?php
/**
 * @link https://github.com/devzyj/yii2-adminlte
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use backend\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

// 注册资源文件。
AppAsset::register($this);
?>
<?php $this->beginContent('@devzyj/yii2/adminlte/views/layouts/main.php'); ?>
<?= $content ?>
<?php $this->endContent(); ?>
