<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBin\controllers;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * TokenController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class TokenController extends \apiCgiBin\components\ApiController
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            // 客户端授权模式。
            'client-credentials' => [
                'class' => 'apiCgiBin\components\actions\ClientCredentialsAction',
            ],
        ];
    }
    
    /**
     * 入口动作。
     */
    public function actionIndex()
    {
        $request = Yii::$app->getRequest();
        $params = $request->getQueryParams();
        $grantType = $request->getBodyParam('grant_type');
        
        if ($grantType === 'client_credentials') {
            // 客户端授权模式。
            return $this->runAction('client-credentials', $params);
        }
        
        throw new BadRequestHttpException('The grant type is unauthorized for this client.');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['POST'],
            'client-credentials' => ['POST'],
        ];
    }
}
