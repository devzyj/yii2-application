<?php
/* @var $this \yii\web\View */
/* @var $form \yii\bootstrap\ActiveForm */
/* @var $user \yii\web\User */
/* @var $model \devjerry\yii2\oauth2\server\DemoAuthorizationForm */
/* @var $clientEntity \devjerry\yii2\oauth2\server\entities\ClientEntity  */
/* @var $scopeEntities \devjerry\yii2\oauth2\server\entities\ScopeEntity[]  */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use devjerry\yii2\oauth2\server\assets\AppAsset;

AppAsset::register($this);
$this->title = 'OAuth2 Authorization';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <div class="container">
        <div class="site-login">
            <h2><?= Html::encode($this->title) ?></h2>
            
            <p>Client: <?= Html::encode($clientEntity->getIdentifier()) ?></p>
            
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>
            
                <?php 
                    $user->getIdentity();
                    $authorizationContent[] = 'asdasdasd';
                    $loginContent[] = $form->field($model, 'username')->textInput(['autofocus' => true]);
                    $loginContent[] = $form->field($model, 'password')->passwordInput();
                ?>
            
                <?= Tabs::widget([
                    'items' => [
                        [
                            'label' => 'Authorization',
                            'content' => implode('', $authorizationContent),
                            'active' => true,
                            'linkOptions' => ['data-authorization-mode' => $model::MODE_USER],
                        ],
                        [
                            'label' => 'Login',
                            'content' => implode('', $loginContent),
                            'linkOptions' => ['data-authorization-mode' => $model::MODE_LOGIN],
                        ],
                    ],
                    'clientEvents' => [
                        'shown.bs.tab' => 'function (e) {
                            console.log(e.target);
                        }',
                    ],
                ]); ?>
        
                <div class="form-group">
                    <div class="col-lg-offset-1 col-lg-11">
                        <?= Html::submitButton('Authorization', ['class' => 'btn btn-primary', 'name' => 'authorization-button']) ?>
                    </div>
                </div>
        
                <?php 
                    $items = [];
                    foreach ($scopeEntities as $scope) {
                        $items[$scope->getIdentifier()] = $scope->name;
                    }
                    
                    echo $form->field($model, 'scopes')->checkboxList($items);
                ?>
                
                <div style='color:red;'>
                    <?= $form->errorSummary($model) ?>
                </div>
                
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>