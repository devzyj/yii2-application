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
use backendApiRbacV1\models\RbacClient;
use backendApiRbacV1\models\RbacClientSearch;
use backendApiRbacV1\behaviors\QueryClientIdBehavior;
use backendApiRbacV1\behaviors\QueryJoinWithBehavior;

/**
 * 客户端控制器。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientController extends \backendApiRbacV1\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = RbacClient::class;

    /**
     * {@inheritdoc}
     */
    public $searchModelClass = RbacClientSearch::class;
    
    /**
     * {@inheritdoc}
     */
    public $createScenario = RbacClient::SCENARIO_INSERT;

    /**
     * {@inheritdoc}
     */
    public $updateScenario = RbacClient::SCENARIO_UPDATE;
    
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
                // 通过判断客户端类型，为查询对像添加 `id` 过滤条件的行为。
                'as queryClientIdBehavior' => [
                    'class' => QueryClientIdBehavior::class,
                    'attribute' => $searchAttributeFieldMap['id'],
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
        ]);
    }
}