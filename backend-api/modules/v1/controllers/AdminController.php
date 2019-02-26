<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiV1\controllers;

use yii\helpers\ArrayHelper;
use backendApiV1\models\Admin;
use backendApiV1\models\AdminSearch;

/**
 * AdminController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AdminController extends \backendApiV1\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = Admin::class;

    /**
     * {@inheritdoc}
     */
    public $searchModelClass = AdminSearch::class;
    
    /**
     * {@inheritdoc}
     */
    public $createScenario = Admin::SCENARIO_INSERT;

    /**
     * {@inheritdoc}
     */
    public $updateScenario = Admin::SCENARIO_UPDATE;

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
                // 即时加载指定的额外资源。
                'as eagerLoadingBehavior' => [
                    'class' => 'devzyj\rest\behaviors\EagerLoadingBehavior',
                ],
            ],
        ]);
    }
}