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
        $url = 'http://auth.backend.application.yii2.devzyj.zyj/oauth2/authorize';
        $params = [
            'response_type' => '', 
            'client_id' => 'f4c22926e400ebca', 
            'scope' => 'basic', 
            'state' => 'abc123',
            'redirect_uri' => Url::toRoute('/test/oauth-callback', true),
        ];
        
        $params['response_type'] = 'code';
        $content[] = Html::a('授权码模式', $url . '?' . http_build_query($params), ['target' => '_blank']);
        
        $params['response_type'] = 'token';
        $content[] = Html::a('简化模式', $url . '?' . http_build_query($params), ['target' => '_blank']);
        
        return implode(' | ', $content);
    }
    
    public function actionOauthCallback()
    {
        print_r(Yii::$app->getRequest()->getQueryParams());
    }
}
