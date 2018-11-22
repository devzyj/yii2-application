<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBinV1\components;

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
}
