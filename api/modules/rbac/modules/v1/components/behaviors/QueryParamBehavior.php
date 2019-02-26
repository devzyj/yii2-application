<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\behaviors;

use Yii;

/**
 * QueryParamBehavior 是为查询对像添加 URL 查询参数中的过滤条件的行为。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class QueryParamBehavior extends \yii\base\Behavior
{
    /**
     * @var array 查询参数名称与字段的映射。
     */
    public $paramMap = [];
    
    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            \devzyj\rest\Action::EVENT_AFTER_PREPARE_DATA_PROVIDER => 'afterPrepareDataProvider',
        ];
    }
    
    /**
     * @param \devzyj\rest\ActionEvent $event
     * @see \devzyj\rest\Action::afterPrepareDataProvider()
     */
    public function afterPrepareDataProvider($event)
    {
        /* @var $query \yii\db\ActiveQuery */
        $query = $event->object->query;
        
        $request = Yii::$app->getRequest();
        foreach ($this->paramMap as $param => $field) {
            $value = $request->getQueryParam($param);
            if ($value !== null) {
                $query->andFilterCompare($field, $value);
            }
        }
    }
}
