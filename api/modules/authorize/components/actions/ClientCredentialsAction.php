<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiAuthorize\components\actions;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use api\models\Client;

/**
 * ClientCredentialsAction 实现了客户端模式的授权方式。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsAction extends \yii\base\Action
{
    /**
     * 生成令牌。
     * 
     * @throws \yii\web\BadRequestHttpException 缺少必要的参数。
     * @throws \yii\web\NotFoundHttpException 客户端不存在，或不可用，或密钥错误。
     * @return array
     */
    public function run()
    {
        $params = Yii::$app->getRequest()->getBodyParams();
        if (!isset($params['client_id']) || !isset($params['client_secret'])) {
            throw new BadRequestHttpException('Missing required parameters: client_id, client_secret.');
        }
        
        /* @var $model Client */
        $model = Client::findOrSetOneById($params['client_id']);
        if (!$model || $params['client_secret'] !== $model->secret) {
            throw new NotFoundHttpException('The `client_id` or `client_secret` invalid.');
        } elseif (!$model->getIsValid()) {
            throw new NotFoundHttpException('Client is invalid.');
        }
        
        /* @var $module \apiAuthorize\Module */
        $module = $this->controller->module;

        // 生成并返回令牌。
        return $module->getToken()->generateClientCredentials($model);
    }
}