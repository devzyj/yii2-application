<?php
/**
 * @link https://github.com/devzyj/yii2-adminlte
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use devjerry\yii2\adminlte\web\AdminLteAsset;
use devjerry\yii2\adminlte\helpers\AdminLteHelper;

/* @var $this \yii\web\View */
/* @var $content string */

// 注册资源文件。
AdminLteAsset::register($this);
AdminLteHelper::registerExtraAssets($this);
?>
<?php $this->beginContent('@devjerry/yii2/adminlte/views/layouts/basic.php'); ?>
<body class="hold-transition <?= AdminLteHelper::skinClass() ?> <?= AdminLteHelper::layoutClass() ?>">
<?php $this->beginBody() ?>
    <div class="wrapper">
        <!-- Main Header -->
        <?= $this->render('_main/header.php') ?>
        
        <!-- Left side column. contains the logo and sidebar -->
        <?= $this->render('_main/sidebar.php') ?>
        
        <!-- Content Wrapper. Contains page content -->
        <?= $this->render('_main/content.php', ['content' => $content]) ?>
        
        <!-- Main Footer -->
        <?= $this->render('_main/footer.php') ?>
        
        <!-- Control Sidebar -->
        <?= $this->render('_main/control.php') ?>
    </div>
<?php $this->endBody() ?>
</body>
<?php $this->endContent(); ?>