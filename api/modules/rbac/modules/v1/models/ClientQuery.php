<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

/**
 * This is the ActiveQuery class for [[Client]].
 * 
 * @see Client
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientQuery extends \yii\db\ActiveQuery
{
    /**
     * 禁用的客户端。
     * 
     * @return $this 查询对象本身。
     */
    public function disabled()
    {
        return $this->andWhere([
            $this->getPrimaryTableName() . '.status' => Client::STATUS_DISABLED,
        ]);
    }

    /**
     * 启用的客户端。
     *
     * @return $this 查询对象本身。
     */
    public function enabled()
    {
        return $this->andWhere([
            $this->getPrimaryTableName() . '.status' => Client::STATUS_ENABLED,
        ]);
    }
    
    /**
     * 有效的客户端。
     * 
     * @return $this 查询对象本身。
     */
    public function valid()
    {
        return $this->enabled();
    }
    
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
