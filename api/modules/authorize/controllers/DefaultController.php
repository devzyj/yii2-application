<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiAuthorize\controllers;

/**
 * DefaultController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class DefaultController extends \apiAuthorize\components\Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            // 客户端授权。
            'client-credentials' => [
                'class' => 'apiAuthorize\components\actions\ClientCredentialsAction',
            ]
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'client-credentials' => ['POST'],
        ];
    }
}
