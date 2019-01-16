<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model devjerry\yii2\oauth2\server\models\LoginForm */
/* @var $client devjerry\yii2\oauth2\server\entities\ClientEntity  */
/* @var $scopes devjerry\yii2\oauth2\server\entities\ScopeEntity[]  */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Authorization Login';
?>
<div class="site-login">
    <h2><?= Html::encode($client->name) ?></h2>
    
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Authorization', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php 
            $items = [];
            foreach ($scopes as $scope) {
                $items[$scope->identifier] = $scope->identifier;
            }
            
            echo $form->field($model, 'scopes')->checkboxList($items);
        ?>
        
    <?php ActiveForm::end(); ?>
</div>
