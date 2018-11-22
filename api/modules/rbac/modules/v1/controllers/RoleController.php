<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use apiRbacV1\models\Role;
use apiRbacV1\models\RoleSearch;

/**
 * 角色控制器。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RoleController extends \apiRbacV1\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = Role::class;

    /**
     * {@inheritdoc}
     */
    public $searchModelClass = RoleSearch::class;

    /**
     * {@inheritdoc}
     */
    public $createScenario = Role::SCENARIO_INSERT;
    
    /**
     * {@inheritdoc}
     */
    public $updateScenario = Role::SCENARIO_UPDATE;
    
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
            // 分配用户。
            'assign-user' => [
                'class' => 'apiRbacV1\components\actions\AssignAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'users',
                'relationModelClass' => 'apiRbacV1\models\User',
            ],
            // 移除用户。
            'remove-user' => [
                'class' => 'apiRbacV1\components\actions\RemoveAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'users',
            ],
            // 分配多个用户。
            'assign-users' => [
                'class' => 'apiRbacV1\components\actions\AssignMultipleAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'users',
                'relationModelClass' => 'apiRbacV1\models\User',
            ],
            // 移除多个用户。
            'remove-users' => [
                'class' => 'apiRbacV1\components\actions\RemoveMultipleAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'users',
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
            'assign-user' => ['POST'],
            'remove-user' => ['DELETE'],
            'assign-users' => ['POST'],
            'remove-users' => ['DELETE'],
        ]);
    }
}