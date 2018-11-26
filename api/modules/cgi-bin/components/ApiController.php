<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBin\components;

use Yii;
use yii\helpers\ArrayHelper;
use apiCgiBin\components\Identity;

/**
 * ApiController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ApiController extends \api\components\ApiController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'authMethods' => [
                    'httpBasicAuth' => [
                        'class' => 'yii\filters\auth\HttpBasicAuth',
                        'auth' => function ($username, $password) {
                            /* @var $model Identity */
                            $model = Identity::findOrSetOneById($username);
                            if ($model->secret === $password && $model->getClientIsValid()) {
                                return $model;
                            }
                        },
                    ],
                ]
            ],
        ]);
    }
}