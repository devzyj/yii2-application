<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiV1\models;

/**
 * ClientSearch class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientSearch extends Client
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_time', 'status', 'token_expires_in', 'rate_limit_count', 'rate_limit_seconds'], 'integer'],
            [['id', 'name', 'secret', 'description', 'allowed_ips', 'allowed_apis'], 'string'],
        ];
    }
}
