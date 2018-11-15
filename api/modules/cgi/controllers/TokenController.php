<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgi\controllers;

/**
 * Token controller.
 */
class TokenController extends \apiCgi\components\ApiController
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'create-json-web-token' => [
                'class' => 'apiCgi\components\actions\CreateJsonWebTokenAction',
            ]
        ];
    }
}
