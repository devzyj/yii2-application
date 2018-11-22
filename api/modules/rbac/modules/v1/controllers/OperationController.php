<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use apiRbacV1\models\Operation;
use apiRbacV1\models\OperationSearch;

/**
 * 操作控制器。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OperationController extends \apiRbacV1\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = Operation::class;

    /**
     * {@inheritdoc}
     */
    public $searchModelClass = OperationSearch::class;
    
    /**
     * {@inheritdoc}
     */
    public $createScenario = Operation::SCENARIO_INSERT;
    
    /**
     * {@inheritdoc}
     */
    public $updateScenario = Operation::SCENARIO_UPDATE;
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'dataFilter' => [
                    'class' => 'yii\data\ActiveDataFilter',
                    'searchModel' => $this->searchModelClass,
                ],
                // 通过判断客户端类型，为查询对像添加 `client_id` 过滤条件的行为。
                'as queryClientIdBehavior' => [
                    'class' => 'apiRbacV1\components\behaviors\QueryClientIdBehavior',
                ],
                // 为查询对像添加 URL 查询参数中的过滤条件的行为。
                'as queryParamBehavior' => [
                    'class' => 'apiRbacV1\components\behaviors\QueryParamBehavior',
                    'paramMap' => [
                        'clientid' => 'client_id',
                    ],
                ],
                // 通过遍历查询条件中的数据表名，自动使用 [[joinWith()]]。
                'as queryJoinWithBehavior' => [
                    'class' => 'apiRbacV1\components\behaviors\QueryJoinWithBehavior',
                ],
                // 即时加载指定的额外资源。
                'as eagerLoadingBehavior' => [
                    'class' => 'devzyj\rest\behaviors\EagerLoadingBehavior',
                ],
            ],
            'create' => [
                // 通过判断客户端类型，为数据模型加载适当的 `client_id` 的行为。
                'as loadClientIdBehavior' => [
                    'class' => 'apiRbacV1\components\behaviors\LoadClientIdBehavior',
                ]
            ],
            'batch-create' => [
                // 通过判断客户端类型，为数据模型加载适当的 `client_id` 的行为。
                'as loadClientIdBehavior' => [
                    'class' => 'apiRbacV1\components\behaviors\LoadClientIdBehavior',
                ]
            ],
            'create-validate' => [
                // 通过判断客户端类型，为数据模型加载适当的 `client_id` 的行为。
                'as loadClientIdBehavior' => [
                    'class' => 'apiRbacV1\components\behaviors\LoadClientIdBehavior',
                ]
            ],
            // 分配权限。
            'assign-permission' => [
                'class' => 'apiRbacV1\components\actions\AssignAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'permissions',
                'relationModelClass' => 'apiRbacV1\models\Permission',
            ],
            // 移除权限。
            'remove-permission' => [
                'class' => 'apiRbacV1\components\actions\RemoveAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'permissions',
            ],
            // 分配多个权限。
            'assign-permissions' => [
                'class' => 'apiRbacV1\components\actions\AssignMultipleAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'permissions',
                'relationModelClass' => 'apiRbacV1\models\Permission',
            ],
            // 移除多个权限。
            'remove-permissions' => [
                'class' => 'apiRbacV1\components\actions\RemoveMultipleAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'permissions',
            ],
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'assign-permission' => ['POST'],
            'remove-permission' => ['DELETE'],
            'assign-permissions' => ['POST'],
            'remove-permissions' => ['DELETE'],
        ]);
    }
}