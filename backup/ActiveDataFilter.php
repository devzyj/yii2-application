<?php
/**
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 */
namespace v1\components;

use yii\helpers\ArrayHelper;

/**
 * ActiveDataFilter 允许在 [[\yii\db\QueryInterface::where()]] 中组合过滤条件。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ActiveDataFilter extends \yii\data\ActiveDataFilter
{
    /**
     * @var array|false 查询条件 `LIKE` 的转义替换。
     * @see \yii\db\conditions\LikeCondition::$escapingReplacements
     */
    public $likeEscapingReplacements;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // 执行父类程序。
        parent::init();
        
        // 操作符支持的属性类型列表。
        $this->operatorTypes = ArrayHelper::merge($this->operatorTypes, [
            '<' => '*',
            '>' => '*',
            '<=' => '*',
            '>=' => '*',
            'LIKE' => '*',
        ]);
        
        // 重新定义构建一个 `LIKE` 查询条件所使用的方法。
        $this->conditionBuilders['LIKE'] = 'buildLikeCondition';
    }

    /**
     * 构建一个 `LIKE` 查询条件。
     * 
     * @param string $operator 操作符关键字。
     * @param mixed $condition 属性的条件。
     * @param string $attribute 属性名称。
     * @return array
     */
    protected function buildLikeCondition($operator, $condition, $attribute)
    {
        $condition = $this->buildOperatorCondition($operator, $condition, $attribute);
        
        // 如果转义替换不为 `null`。
        if ($this->likeEscapingReplacements !== null) {
            $condition[] = $this->likeEscapingReplacements;
        }
        
        return $condition;
    }
}
