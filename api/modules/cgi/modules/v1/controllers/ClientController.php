<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiV1\controllers;

use yii\helpers\ArrayHelper;
use apiCgiV1\models\Client;
use apiCgiV1\models\ClientSearch;

/**
 * ClientController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientController extends \apiCgiV1\components\ActiveController
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
            // 重置ID。
            'reset-id' => [
                'class' => 'devzyj\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => Client::SCENARIO_RESET_ID,
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'on beforeProcessModel' => function ($event) {
                    // 重置ID时设置为空。
                    $event->object->id = null;
                }
            ],
            // 重置密钥。
            'reset-secret' => [
                'class' => 'devzyj\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => Client::SCENARIO_RESET_SECRET,
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
                'on beforeProcessModel' => function ($event) {
                    // 重置密钥时设置密钥为空。
                    $event->object->secret = null;
                }
            ],
            // 删除缓存。
            'delete-cache' => [
                'class' => 'apiCgiV1\components\actions\clients\DeleteCacheAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'notFoundMessage' => $this->notFoundMessage,
            ],
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'reset-id' => ['PUT'],
            'reset-secret' => ['PUT'],
            'delete-cache' => ['DELETE'],
        ]);
    }
}