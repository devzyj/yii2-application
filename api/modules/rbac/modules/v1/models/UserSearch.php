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
 * 查询用户数据模型。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class UserSearch extends User implements QueryJoinWithBehaviorInterface
{
    use QueryJoinWithBehaviorTrait;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'create_time'], 'integer'],
            [['account', 'description'], 'string'],
        ];
    }
}
