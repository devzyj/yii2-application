<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiV1\models;

/**
 * AdminSearch class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AdminSearch extends Admin
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'create_time', 'status'], 'integer'],
            [['username', 'nickname', 'email', 'mobile', 'allowed_ips'], 'string'],
        ];
    }
}
