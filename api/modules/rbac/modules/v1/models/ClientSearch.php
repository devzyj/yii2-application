<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

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
            [['id', 'create_time', 'status', 'token_expires_in', 'rate_limit_count', 'rate_limit_seconds'], 'integer'],
            [['name', 'description', 'identifier', 'secret', 'allowed_ips', 'allowed_apis'], 'string'],
        ];
    }
}
