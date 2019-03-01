<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Title</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                <i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <?= $this->context->action->uniqueId ?>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">Footer</div>
    <!-- /.box-footer-->
</div>
<!-- /.box -->