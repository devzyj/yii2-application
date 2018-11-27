<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
/**
 * 用户登录后使用的布局文件。
 */
use library\adminlte\web\AdminLteAsset;
use library\adminlte\web\FontAsset;
use library\adminlte\helpers\AdminLteHelper;

/* @var $this \yii\web\View */
/* @var $appAssetClass \yii\web\AssetBundle */
/* @var $content string */

// 注册主要资源。
AdminLteAsset::register($this);

// 注册字体资源。
FontAsset::register($this);

// 注册应用资源（需要根据应用做相应的调整）。
if (class_exists('backend\assets\AppAsset')) {
    // yii2-app-advanced
    $appAssetClass = 'backend\assets\AppAsset';

    // 注册 yii2-app-advanced 资源。
    $appAssetClass::register($this);
} elseif (class_exists('app\assets\AppAsset')) {
    // yii2-app-basic
    $appAssetClass = 'app\assets\AppAsset';

    // 注册 yii2-app-basic 资源。
    $appAssetClass::register($this);
}

// 获取配置的皮肤名称。
$skin = AdminLteHelper::skinClass();
?>
<?php $this->beginContent(__DIR__ . '/base.php') ?>
<body class="hold-transition <?= $skin ?> sidebar-mini">
<?php $this->beginBody() ?>
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Header -->
        <?= $this->render('_common/header.php') ?>
        
        <!-- Left side column. contains the sidebar -->
        <?= $this->render('_common/sidebar.php') ?>
        
        <!-- Content Wrapper. Contains page content -->
        <?= $this->render('_common/content.php', ['content' => $content]) ?>
        
        <!-- Footer -->
        <?= $this->render('_common/footer.php') ?>
        
        <!-- Control Sidebar -->
        <?= $this->render('_common/control.php') ?>
    </div>
    <!-- ./wrapper -->
<?php $this->endBody() ?>
</body>
<?php $this->endContent() ?>