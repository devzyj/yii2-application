<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\behaviors;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * QueryJoinWithBehavior 实现了通过遍历查询条件中的数据表名，自动使用 [[joinWith()]]。
 * 
 * QueryJoinWithBehavior 需要查询数据的模型实现 [[QueryJoinWithBehaviorInterface]]。
 * 如果查询数据的模型没有设置或没有实现 [[QueryJoinWithBehaviorInterface]]， QueryJoinWithBehavior 将什么也不做。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class QueryJoinWithBehavior extends \yii\base\Behavior
{
    /**
     * @var boolean 是否即时加载，默认为 `false`。
     * @see \yii\db\ActiveQuery::joinWith()
     */
    public $eagerLoading = false;
    
    /**
     * @var string 连接类型，默认为 `LEFT JOIN`。
     * @see \yii\db\ActiveQuery::joinWith()
     */
    public $joinType = 'LEFT JOIN';
    
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
        $modelClass = $query->modelClass;
        $model = $modelClass::instance();
        if ($model instanceof QueryJoinWithBehaviorInterface) {
            $tables = $this->getQueryTables($query->where);
            if ($tables) {
                $joinWith = $model->getQueryJoinWithByTables(array_keys($tables), $query);
                if ($joinWith) {
                    $query->joinWith($joinWith, $this->eagerLoading, $this->joinType);
                }
            }
        }
    }
    
    /**
     * 从查询条件中遍历出全部数据表名。
     * 
     * @param array $where
     * @return array
     */
    protected function getQueryTables($where)
    {
        $result = [];
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $result = ArrayHelper::merge($result, $this->getQueryTables($value));
                    continue;
                }
                
                if (is_int($key)) {
                    // 索引为数字时，字段为数组的值。
                    $field = $value;
                } else {
                    // 索引为字符串时，字段为数组的索引。
                    $field = $key;
                }
                
                // 如果使用了 `tableName.field` 的格式。
                if (strpos(trim($field, '.'), '.') !== false) {
                    $table = current(explode('.', $field));
                    $result[$table] = true;
                }
            }
        }
        
        return $result;
    }
}
