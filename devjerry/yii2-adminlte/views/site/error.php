<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use Yii;
use yii\helpers\Html;
use yii\web\HttpException;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception \Exception */

// 页面标题。
$this->title = $name;

// 不显示内容头部标题。
$this->beginBlock('content-header');
$this->endBlock();

// 根据错误类型，确认图标颜色。
$color = $exception instanceof HttpException ? 'text-yellow' : 'text-red';
?>
<div class="error-page">
    <h2 class="headline text-info"><i class="fa fa-warning <?= $color ?>"></i></h2>
    <div class="error-content">
        <h3><?= Html::encode($name) ?></h3>
        <p><?= nl2br(Html::encode($message)) ?></p>
        <p>
            <small>
                <?= Yii::t('adminlte', 'The above error occurred while the Web server was processing your request.') ?>
                <?= Yii::t('adminlte', 'Please contact us if you think this is a server error.') ?>
                <?= Yii::t('adminlte', 'Meanwhile, you may {returnHomePage} or try using the search form.', [
                        'returnHomePage' => Html::a(Yii::t('adminlte', 'return to home'), Yii::$app->homeUrl),
                    ])
                ?>
            </small>
        </p>
        <form class='search-form'>
            <div class='input-group'>
                <input type="text" name="search" class='form-control' placeholder="Search"/>
                <div class="input-group-btn">
                    <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>