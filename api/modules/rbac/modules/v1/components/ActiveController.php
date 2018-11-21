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
            // 检查客户端状态。
            'clientStatusFilter' => [
                'class' => 'api\components\filters\ClientStatusFilter',
            ],
            // 检查客户端允许访问的 IPs。
            'clientIpsFilter' => [
                'class' => 'api\components\filters\ClientIpsFilter',
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
     */
    public function checkActionAccess($action, $params = [])
    {
        /* @var $identity \api\components\Identity */
        if (!($user = Yii::$app->getUser()) || !($identity = $user->getIdentity(false))) {
            throw new ForbiddenHttpException('Client must be logged in.');
        }
        
        // 检查客户端允许访问的 API。
        if (!$identity->checkClientAPIs($action->getUniqueId())) {
            throw new ForbiddenHttpException('Client API limit.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkModelAccess($model, $action, $params = [])
    {
        
    }
}
