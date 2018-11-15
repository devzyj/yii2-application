<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgi\components;

use yii\helpers\ArrayHelper;

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
                    [
                        'class' => '\yii\filters\auth\QueryParamAuth',
                        'tokenParam' => 'clientid',
                    ],
                ],
            ],
        ]);
    }
    
}