<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiOauthV1\models;

/**
 * This is the ActiveQuery class for [[OauthClient]].
 * 
 * @see OauthClient
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClientQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * 
     * @return OauthClient[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * 
     * @return OauthClient|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
