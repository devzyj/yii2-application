<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Test controller.
 */
class TestController extends \yii\web\Controller
{
    public function actionOauth()
    {
        $url = 'http://api.backend.application.yii2.devzyj.zyj/oauth2/authorize';
        $params = [
            'response_type' => 'code', 
            'client_id' => 'f4c22926e400ebca', 
            'scope' => 'basic basic3', 
            'state' => 'abc123',
            'redirect_uri' => Url::toRoute('/test/oauth-callback', true),
        ];
        
        return Html::a('授权', $url . '?' . http_build_query($params), ['target' => '_blank']);
    }
    
    public function actionOauthCallback()
    {
        var_dump(Yii::$app->getRequest()->getQueryParams());
    }
}
