<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components\rest;

use Yii;
use yii\helpers\ArrayHelper;

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
}
