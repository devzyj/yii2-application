<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataFilter;
use devzyj\rest\behaviors\EagerLoadingBehavior;
use backendApiRbacV1\models\RbacOperation;
use backendApiRbacV1\models\RbacOperationSearch;
use backendApiRbacV1\models\RbacPermission;
use backendApiRbacV1\behaviors\QueryClientIdBehavior;
use backendApiRbacV1\behaviors\QueryParamBehavior;
use backendApiRbacV1\behaviors\QueryJoinWithBehavior;
use backendApiRbacV1\behaviors\LoadClientIdBehavior;
use backendApiRbacV1\actions\AssignAction;
use backendApiRbacV1\actions\RemoveAction;
use backendApiRbacV1\actions\AssignMultipleAction;
use backendApiRbacV1\actions\RemoveMultipleAction;

/**
 * 操作控制器。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OperationController extends \backendApiRbacV1\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = RbacOperation::class;

    /**
     * {@inheritdoc}
     */
    public $searchModelClass = RbacOperationSearch::class;
    
    /**
     * {@inheritdoc}
     */
    public $createScenario = RbacOperation::SCENARIO_INSERT;
    
    /**
     * {@inheritdoc}
     */
    public $updateScenario = RbacOperation::SCENARIO_UPDATE;
    
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
                        'permissionid' => $searchAttributeFieldMap['permission_id'],
                        'roleid' => $searchAttributeFieldMap['role_id'],
                        'userid' => $searchAttributeFieldMap['user_id'],
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
            // 分配权限。
            'assign-permission' => [
                'class' => AssignAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'rbacPermissions',
                'relationModelClass' => RbacPermission::class,
            ],
            // 移除权限。
            'remove-permission' => [
                'class' => RemoveAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'rbacPermissions',
            ],
            // 分配多个权限。
            'assign-permissions' => [
                'class' => AssignMultipleAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'rbacPermissions',
                'relationModelClass' => RbacPermission::class,
            ],
            // 移除多个权限。
            'remove-permissions' => [
                'class' => RemoveMultipleAction::class,
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'relationName' => 'rbacPermissions',
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