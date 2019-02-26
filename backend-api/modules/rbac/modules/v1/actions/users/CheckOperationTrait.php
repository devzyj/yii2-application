<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\actions\users;

use backendApiRbacV1\models\RbacUser;
use backendApiRbacV1\models\RbacOperationQuery;
use backendApiRbacV1\models\RbacPermissionQuery;
use backendApiRbacV1\models\RbacRoleQuery;
use backendApiRbacV1\models\RbacUserQuery;

/**
 * CheckOperationTrait
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait CheckOperationTrait
{
    /**
     * 准备查询对像。
     * 
     * @param RbacOperationQuery $query 查询对像。
     * @param RbacUser $model 用户模型。
     * @return RbacOperationQuery
     */
    protected function prepareQuery($query, $model)
    {
        // 查询条件。
        $query->client($model->client_id);
        $query->valid();
        
        // 关联查询。
        $query->joinWith([
            'rbacPermissions' => function ($query) use ($model) {
                /* @var $query RbacPermissionQuery */
                $query->client($model->client_id);
                $query->valid();
                $query->joinWith([
                    'rbacRoles' => function ($query) use ($model) {
                        /* @var $query RbacRoleQuery */
                        $query->client($model->client_id);
                        $query->valid();
                        $query->joinWith([
                            'rbacUsers' => function ($query) use ($model) {
                                /* @var $query RbacUserQuery */
                                $query->andWhere([
                                    $model::tableName() . '.id' => $model->id,
                                ]);
                            }
                        ]);
                    }
                ]);
            },
        ], false);
        
        // 返回查询对像。
        return $query;
    }
}
