<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use apiRbacV1\models\Client;
use apiRbacV1\models\ClientSearch;

/**
 * 客户端控制器。
 * 
 * 只允许`超级客户端`访问。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientController extends \apiRbacV1\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = Client::class;

    /**
     * {@inheritdoc}
     */
    public $searchModelClass = ClientSearch::class;
    
    /**
     * {@inheritdoc}
     */
    public $createScenario = Client::SCENARIO_INSERT;

    /**
     * {@inheritdoc}
     */
    public $updateScenario = Client::SCENARIO_UPDATE;
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'dataFilter' => [
                    'class' => 'yii\data\ActiveDataFilter',
                    'searchModel' => $this->searchModelClass,
                ],
                // 即时加载指定的额外资源。
                'as eagerLoadingBehavior' => [
                    'class' => 'devzyj\rest\behaviors\EagerLoadingBehavior',
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function checkActionAccess($action, $params = [])
    {
        parent::checkActionAccess($action, $params);

        /* @var $identity \apiRbacV1\components\Identity */
        if (!($user = Yii::$app->getUser()) || !($identity = $user->getIdentity(false)) 
                || !($client = $identity->getRbacClient()) || !$client->getIsSuper()) {
            // 只有超级客户端才能访问。
            throw new ForbiddenHttpException('Only super client can access.');
        }
    }
}