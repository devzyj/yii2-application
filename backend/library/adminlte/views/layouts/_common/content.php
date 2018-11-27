<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\Breadcrumbs;
use library\adminlte\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <!-- Content Title -->
        <?php if (isset($this->blocks['content-header'])): ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php else: ?>
            <h1>
                <?php
                    if ($this->title !== null) {
                        echo Html::encode($this->title);
                    } else {
                        echo Inflector::camel2words(Inflector::id2camel($this->context->module->id));
                        echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                    }
                ?>
            </h1>
        <?php endif; ?>
        
        <!-- Breadcrumb -->
        <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Alert box -->
        <?= Alert::widget()?>
        
        <!-- Content box -->
        <?= $content?>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->