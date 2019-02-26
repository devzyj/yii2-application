<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataFilter;
use devzyj\rest\behaviors\EagerLoadingBehavior;
use backendApiRbacV1\models\RbacUser;
use backendApiRbacV1\models\RbacUserSearch;
use backendApiRbacV1\models\RbacRole;
use backendApiRbacV1\behaviors\QueryClientIdBehavior;
use backendApiRbacV1\behaviors\QueryParamBehavior;
use backendApiRbacV1\behaviors\QueryJoinWithBehavior;
use backendApiRbacV1\behaviors\LoadClientIdBehavior;
use backendApiRbacV1\actions\AssignAction;
use backendApiRbacV1\actions\RemoveAction;
use backendApiRbacV1\actions\AssignMultipleAction;
use backendApiRbacV1\actions\RemoveMultipleAction;
use backendApiRbacV1\actions\users\CheckOperationAction;
use backendApiRbacV1\actions\users\CheckOperationsAction;

/**
 * 用户控制器。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UserController extends \backendApiRbacV1\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = RbacUser::class;

    /**
     * {@inheritdoc}
     */
    public $searchModelClass = RbacUserSearch::class;

    /**
     * {@inheritdoc}
     */
    public $createScenario = RbacUser::SCENARIO_INSERT;
    
    /**
     * {@inheritdoc}
     */
    public $updateScenario = RbacUser::SCENARIO_UPDATE;
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $searchModelClass = $this->searchModelClass;
        $searchAttributeFieldMap = $searchModelClass::instance()->searchAttributeFieldMap();
        
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'dataFilter' => [
                    'class' => ActiveDataFilter::class,
                    'searchModel' => $searchModelClass,
                    'attributeMap' => $searchAttributeFieldMap,
                ],
                // 通过判断客户端类型，为查询对像添加 `client_id` 过滤条件的行为。
                'as queryClientIdBehavior' => [
                    'class' => QueryClientIdBehavior::class,
                    'attribute' => $searchAttributeFieldMap['client_id'],
                ],
                // 为查询对像添加 URL 查询参数中的过滤条件的行为。
                'as queryParamBehavior' => [
                    'class' => QueryParamBehavior::class,
                    'paramMap' => [
                        'clientid' => $searchAttributeFieldMap['client_id'],
                        'roleid' => $searchAttributeFieldMap['role_id'],
                        'permissionid' => $searchAttributeFieldMap['permission_id'],
                        'operationid' => $searchAttributeFieldMap['operation_id'],
                    ],
                ],
                // 通过遍历查询条件中的数据表名，自动使用 [[joinWith()]]。
                'as queryJoinWithBehavior' => [
                    'class' => QueryJoinWithBehavior::class,
                ],
                // 即时加载指定的额外资源。
                'as eagerLoadingBehavior' => [
                    'class' => EagerLoadingBehavior::class,
                ],
            ],
            'create' => [
                // 通过判断客户端类型，为数据模型加载适当的 `client_id` 的行为。
                'as loadClientIdBehavior' => [
                    'class' => LoadClientIdBehavior::class,
                ]
            ],
            'batch-create' => [
                // 通过判断客户端类型，为数据模型加载适当的 `client_id` 的行为。
                'as loadClientIdBehavior' => [
                    'class' => LoadClientIdBehavior::class,
                ]
            ],
            'create-validate' => [
                // 通过判断客户端类型，为数据模型加载适当的 `client_id` 的行为。
                'as loadClientIdBehavior' => [
                    'class' => LoadClientIdBehavior::class,
                ]
            ],
            // 分配角色。
            'assign-role' => [
                'class' => AssignAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'rbacRoles',
                'relationModelClass' => RbacRole::class,
            ],
            // 移除角色。
            'remove-role' => [
                'class' => RemoveAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'rbacRoles',
            ],
            // 分配多个角色。
            'assign-roles' => [
                'class' => AssignMultipleAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'rbacRoles',
                'relationModelClass' => RbacRole::class,
            ],
            // 移除多个角色。
            'remove-roles' => [
                'class' => RemoveMultipleAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'rbacRoles',
            ],
            // 检查操作。
            'check-operation' => [
                'class' => CheckOperationAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
            ],
            // 检查多个操作。
            'check-operations' => [
                'class' => CheckOperationsAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
            ],
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'assign-role' => ['POST'],
            'remove-role' => ['DELETE'],
            'assign-roles' => ['POST'],
            'remove-roles' => ['DELETE'],
            'check-operation' => ['GET'],
            'check-operations' => ['GET'],
        ]);
    }
}