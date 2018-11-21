<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

/**
 * This is the ActiveQuery class for [[Operation]].
 *
 * @see Operation
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OperationQuery extends \yii\db\ActiveQuery
{
    /**
     * 添加状态为禁用的条件。
     * 
     * @return $this 查询对象本身
     */
    public function disabled()
    {
        /* @var $modelClass Operation */
        $modelClass = $this->modelClass;
        return $this->andWhere([
            $this->getPrimaryTableName() . '.status' => $modelClass::STATUS_DISABLED,
        ]);
    }

    /**
     * 添加状态为启用的条件。
     * 
     * @return $this 查询对象本身
     */
    public function enabled()
    {
        /* @var $modelClass Operation */
        $modelClass = $this->modelClass;
        return $this->andWhere([
            $this->getPrimaryTableName() . '.status' => $modelClass::STATUS_ENABLED,
        ]);
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
     * @return Operation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * 
     * @return Operation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
