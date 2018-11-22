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
 * 要实现 [[getQueryJoinWithByTables()]] 方法，可以使用 [[QueryJoinWithBehaviorTrait]]。
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
     * @return array 连接关系。
     */
    public function getQueryJoinWithByTables($names);
}
