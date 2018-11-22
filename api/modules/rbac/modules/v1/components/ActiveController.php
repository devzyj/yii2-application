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
class ActiveController extends \devzyj\rest\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // 执行父类程序。
        parent::init();
        
        // 允许批量操作的个数。
        if ($this->allowedCount === null) {
            $this->allowedCount = Yii::$app->params['rest.batch.allowedCount'];
        }
    }
    
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
            // 验证客户端状态是否有效。
            'clientStatusFilter' => [
                'class' => 'api\components\filters\ClientStatusFilter',
            ],
            // 验证客户端 IP 是否被允许访问。
            'clientIpFilter' => [
                'class' => 'api\components\filters\ClientIpFilter',
            ],
            // 验证 RBAC 客户端是否有效。
            'rbacClientFilter' => [
                'class' => 'apiRbacV1\components\filters\ClientFilter',
            ],
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'on afterPrepareDataProvider' => function ($event) {
                    /* @var $dataProvider \yii\data\ActiveDataProvider */
                    $dataProvider = $event->object;
                    
                    // 设置分页。
                    $pagination = $dataProvider->getPagination();
                    $pagination->defaultPageSize = Yii::$app->params['rest.search.pagination.defaultPageSize'];
                    $pagination->pageSizeLimit = Yii::$app->params['rest.search.pagination.pageSizeLimit'];
                    
                    // 设置排序。
                    $sort = $dataProvider->getSort();
                    $sort->enableMultiSort = Yii::$app->params['rest.search.sort.enableMultiSort'];
                },
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
