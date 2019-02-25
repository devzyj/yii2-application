<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * ActiveController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ActiveController extends \api\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            // 身份验证。
            'authenticator' => [
                'authMethods' => [
                    'yii\filters\auth\HttpBearerAuth',
                    'yii\filters\auth\QueryParamAuth',
                ]
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     * 
     * @throws \yii\web\ForbiddenHttpException 客户端没有登录，或者没有访问权限。
     */
    public function checkActionAccess($action, $params = [])
    {
        /* @var $identity \api\components\Identity */
        if (!($user = Yii::$app->getUser()) || !($identity = $user->getIdentity(false)) 
                || !$identity->checkClientAllowedApi($action->getUniqueId())) {
            throw new ForbiddenHttpException('Client API limit.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkModelAccess($model, $action, $params = [])
    {
        /* @var $identity \apiRbacV1\components\Identity */
        if (!($user = Yii::$app->getUser()) || !($identity = $user->getIdentity(false)) 
                || !($client = $identity->getRbacClient())) {
            throw new ForbiddenHttpException('Resources limit.');
        } elseif (!$client->getIsSuper()) {
            if ($model instanceof \common\models\rbac\Client) {
                // 如果模型是 RBAC 客户端，检查模型主键是否等于调用接口的客户端主键。
                if ($model->getPrimaryKey() !== $client->getPrimaryKey()) {
                    throw new ForbiddenHttpException('Resources limit.');
                }
            } else {
                // 如果模型不是 RBAC 客户端，获取模型中的 `client_id` 属性值。
                $clientId = ArrayHelper::getValue($model, 'client_id');
                if ($clientId !== $client->getPrimaryKey()) {
                    throw new ForbiddenHttpException('Resources limit.');
                }
            }
        }
    }
}
