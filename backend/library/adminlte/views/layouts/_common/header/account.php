<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use yii\helpers\Html;

/* @var $this \yii\web\View */
$assetBaseUrl = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <img src="<?= $assetBaseUrl ?>/img/user2-160x160.jpg" class="user-image" alt="User Image">
        <span class="hidden-xs">Alexander Pierce</span>
    </a>
    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
            <img src="<?= $assetBaseUrl ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            <p>
                Alexander Pierce - Web Developer <small>Member since Nov. 2012</small>
            </p>
        </li>
        <!-- Menu Body -->
        <li class="user-body">
            <div class="row">
                <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                </div>
                <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                </div>
                <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                </div>
            </div> <!-- /.row -->
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                <?= Html::a('Profile', ['/profile'], ['class' => 'btn btn-default btn-flat']) ?>
            </div>
            <div class="pull-right">
                <?= Html::a('Sign out', ['/logout'], ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']) ?>
            </div>
        </li>
    </ul>
</li>