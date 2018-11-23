<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\behaviors;

/**
 * QueryJoinWithBehaviorInterface 是可以由查询数据的模型实现的接口，用于自动使用 [[joinWith()]]。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface QueryJoinWithBehaviorInterface
{
    /**
     * 通过数据表名，返回连接关系。
     * 
     * @param array $names 数据表名列表。
     * @param \yii\db\ActiveQuery $query 查询模型。
     * @return string|array 连接关系。参考 [[ActiveQuery::joinWith()]] 中的 `$with` 参数。
     * @see \yii\db\ActiveQuery::joinWith()
     */
    public function getQueryJoinWithByTables($names, $query);
}
