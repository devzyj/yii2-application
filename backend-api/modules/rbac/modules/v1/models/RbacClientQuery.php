<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\models;

/**
 * This is the ActiveQuery class for [[RbacClient]].
 * 
 * @see RbacClient
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RbacClientQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * 
     * @return Client[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * 
     * @return Client|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
