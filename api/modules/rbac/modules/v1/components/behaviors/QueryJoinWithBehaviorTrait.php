<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\behaviors;

use Yii;
use apiRbacV1\models\ClientSearch;
use apiRbacV1\models\UserSearch;
use apiRbacV1\models\RoleSearch;
use apiRbacV1\models\PermissionSearch;
use apiRbacV1\models\OperationSearch;

/**
 * QueryJoinWithBehaviorTrait 实现了 [[QueryJoinWithBehaviorInterface]] 中的方法。
 * 
 * @see QueryJoinWithBehaviorInterface
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait QueryJoinWithBehaviorTrait
{
    /**
     * 通过数据表名，返回连接关系。
     * 
     * @param array $names 数据表名列表。
     * @return array 连接关系。
     */
    public function getQueryJoinWithByTables($names)
    {
        $with = [];
        foreach ($names as $name) {
            switch ($name) {
                case ClientSearch::tableName():
                    $with[] = 'client';
                    break;
                case UserSearch::tableName():
                    $with[] = 'users';
                    break;
                case RoleSearch::tableName():
                    $with[] = 'roles';
                    break;
                case PermissionSearch::tableName():
                    $with[] = 'permissions';
                    break;
                case OperationSearch::tableName():
                    $with[] = 'operations';
                    break;
            }
        }
        
        return $with;
    }
}
