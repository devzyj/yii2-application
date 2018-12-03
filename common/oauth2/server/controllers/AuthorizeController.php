<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\controllers;

use Yii;
use yii\web\BadRequestHttpException;

/**
 * AuthorizeController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizeController extends \yii\web\Controller
{
    /**
     * 授权入口。
     * 
     * @return array 认证信息。
     * @todo 
     */
    public function actionIndex()
    {
        $responseType = Yii::$app->getRequest()->getQueryParam('response_type');
        if (empty($responseType)) {
            throw new BadRequestHttpException('Missing parameters: "response_type" required.');
        }
        
        if ($responseType === 'code') {
            return 'todo authorize code action';
        } elseif ($responseType === 'token') {
            return 'todo authorize token action';
        }
    }
}
