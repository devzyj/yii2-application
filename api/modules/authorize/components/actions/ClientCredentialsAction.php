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
use yii\web\ForbiddenHttpException;
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
     * @throws BadRequestHttpException 缺少必要的参数。
     * @throws NotFoundHttpException 客户端不存在。
     * @throws ForbiddenHttpException 客户端不可用，或密钥错误。
     * @return array
     */
    public function run()
    {
        $params = Yii::$app->getRequest()->getBodyParams();
        if (!isset($params['client_id'])) {
            throw new BadRequestHttpException('Missing required parameters: client_id.');
        } elseif (!isset($params['client_secret'])) {
            throw new BadRequestHttpException('Missing required parameters: client_secret.');
        }
        
        /* @var $model Client */
        $model = Client::findOrSetOneById($params['client_id']);
        if (!$model) {
            throw new NotFoundHttpException('The `client_id` is invalid.');
        } elseif (!$model->getIsValid()) {
            throw new ForbiddenHttpException('The client is invalid.');
        } elseif ($params['client_secret'] !== $model->secret) {
            throw new ForbiddenHttpException('The `client_secret` is invalid.');
        }
        
        /* @var $module \apiAuthorize\Module */
        $module = $this->controller->module;

        // 生成并返回令牌。
        return $module->getToken()->generateClientCredentials($model);
    }
}