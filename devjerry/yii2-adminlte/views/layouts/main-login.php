<?php
/**
 * @link https://github.com/devzyj/yii2-adminlte
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use devjerry\yii2\adminlte\web\BasicAdminLteAsset;

/* @var $this \yii\web\View */
/* @var $content string */

// 注册资源文件。
BasicAdminLteAsset::register($this);
?>
<?php $this->beginContent('@devjerry/yii2/adminlte/views/layouts/basic.php'); ?>
<body class="hold-transition login-page">
<?php $this->beginBody() ?>
    <?= $content ?>
<?php $this->endBody() ?>
</body>
<?php $this->endContent(); ?>