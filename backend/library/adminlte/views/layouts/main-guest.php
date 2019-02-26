<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
/**
 * 用户未登录时使用的布局文件。
 */
use library\adminlte\web\CustomBasicAsset;
use library\adminlte\web\FontAsset;

/* @var $this \yii\web\View */
/* @var $appAssetClass \yii\web\AssetBundle */
/* @var $content string */

// 注册自定义基础的 CSS 资源包。
CustomBasicAsset::register($this);

// 注册字体资源。
FontAsset::register($this);
?>
<?php $this->beginContent(__DIR__ . '/base.php') ?>
<body class="hold-transition guest-page">
<?php $this->beginBody() ?>
    <?= $content ?>
<?php $this->endBody() ?>
</body>
<?php $this->endContent() ?>