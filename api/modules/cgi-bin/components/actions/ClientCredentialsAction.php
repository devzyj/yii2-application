<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBin\components\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * ClientCredentialsAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsAction extends \yii\base\Action
{
    /**
     * 生成客户端的访问令牌。
     * 
     * @throws \yii\web\BadRequestHttpException 缺少必要的参数。
     * @throws \yii\web\NotFoundHttpException 客户端不存在，或不可用，或密钥错误。
     * @return array
     */
    public function run()
    {
        if (!($user = Yii::$app->getUser()) || !($client = $user->getIdentity(false))) {
            throw new ServerErrorHttpException('Client must be logged in.');
        }
        
        /* @var $module \apiCgiBin\Module */
        $module = $this->controller->module;

        // 生成并返回令牌。
        return $module->getToken()->generateClientCredentials($client);
    }
}