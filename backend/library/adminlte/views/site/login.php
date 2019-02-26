<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
/**
 * 用户登录页面。
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use library\adminlte\web\BasicAsset;
use library\adminlte\web\FontAsset;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model yii\base\Model */

// 注册基础的 CSS 资源。
BasicAsset::register($this);

// 注册字体资源。
FontAsset::register($this);

// 页面标题。
$this->title = 'Sign In';

// 用户名输入框设置。
$usernameOptions = [
    'options' => [
        'class' => 'form-group has-feedback'
    ],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

// 密码输入框设置。
$passwordOptions = [
    'options' => [
        'class' => 'form-group has-feedback'
    ],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<body class="hold-transition login-page">
<?php $this->beginBody() ?>
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Admin</b>LTE</a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>
            
            <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
            
            <?= $form->field($model, 'username', $usernameOptions)
                ->label(false)
                ->textInput(['placeholder' => $model->getAttributeLabel('username')])
            ?>

            <?= $form->field($model, 'password', $passwordOptions)
                ->label(false)
                ->passwordInput(['placeholder' => $model->getAttributeLabel('password')])
            ?>

            <div class="row">
                <div class="col-xs-8">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                </div>
                <!-- /.col -->
            </div>
            
            <?php ActiveForm::end(); ?>
    
            <div class="social-auth-links text-center">
                <p>- OR -</p>
                <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
                <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>
            </div>
            <!-- /.social-auth-links -->
    
            <a href="#">I forgot my password</a>
            <br />
            <a href="register.html" class="text-center">Register a new membership</a>
        </div>
        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
<?php $this->endBody() ?>
</body>