<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

/**
 * This is the ActiveQuery class for [[Role]].
 *
 * @see Role
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RoleQuery extends \yii\db\ActiveQuery
{
    /**
     * 禁用的角色。
     * 
     * @return $this 查询对象本身。
     */
    public function disabled()
    {
        return $this->andWhere([
            $this->getPrimaryTableName() . '.status' => Role::STATUS_DISABLED,
        ]);
    }

    /**
     * 启用的角色。
     * 
     * @return $this 查询对象本身。
     */
    public function enabled()
    {
        return $this->andWhere([
            $this->getPrimaryTableName() . '.status' => Role::STATUS_ENABLED,
        ]);
    }
    
    /**
     * 有效的角色。
     * 
     * @return $this 查询对象本身。
     */
    public function valid()
    {
        return $this->enabled();
    }

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
     * @return Role[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * 
     * @return Role|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}