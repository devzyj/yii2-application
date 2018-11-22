<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use apiRbacV1\components\behaviors\QueryJoinWithBehaviorInterface;
use apiRbacV1\components\behaviors\QueryJoinWithBehaviorTrait;

/**
 * 查询客户端数据模型。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientSearch extends Client implements QueryJoinWithBehaviorInterface
{
    use QueryJoinWithBehaviorTrait;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'create_time'], 'integer'],
            [['name', 'identifier', 'description'], 'string'],
        ];
    }
}
