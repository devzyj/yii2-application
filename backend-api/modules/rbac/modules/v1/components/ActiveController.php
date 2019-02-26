<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\components;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * ActiveController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ActiveController extends \backendApi\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public function checkModelAccess($model, $action, $params = [])
    {
        /* @var $identity ClientIdentity */
        if (!($user = Yii::$app->getUser()) || !($identity = $user->getIdentity(false)) || !$identity->checkAllowedModel($model)) {
            throw new ForbiddenHttpException('Resources limit.');
        }
    }
}
