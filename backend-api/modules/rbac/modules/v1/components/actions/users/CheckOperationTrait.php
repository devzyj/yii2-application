<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\actions\users;

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
     * @param \yii\db\ActiveQuery $query 查询对像。
     * @param \apiRbacV1\models\User $model 用户模型。
     * @return \yii\db\ActiveQuery
     */
    protected function prepareQuery($query, $model)
    {
        // 查询条件。
        $query->client($model->client_id);
        $query->valid();
        
        // 关联查询。
        $query->joinWith([
            'permissions' => function ($query) use ($model) {
                /* @var $query \apiRbacV1\models\PermissionQuery */
                $query->client($model->client_id);
                $query->valid();
                $query->joinWith([
                    'roles' => function ($query) use ($model) {
                        /* @var $query \apiRbacV1\models\RoleQuery */
                        $query->client($model->client_id);
                        $query->valid();
                        $query->joinWith([
                            'users' => function ($query) use ($model) {
                                /* @var $query \apiRbacV1\models\UserQuery */
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
