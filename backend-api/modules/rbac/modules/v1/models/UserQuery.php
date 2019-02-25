<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * 添加客户端ID的条件。
     *
     * @return $this 查询对象本身
     */
    public function client($clientId)
    {
        return $this->andWhere([
            $this->getPrimaryTableName() . '.client_id' => $clientId,
        ]);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * 
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
