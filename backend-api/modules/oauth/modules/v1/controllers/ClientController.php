<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiOauthV1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataFilter;
use devzyj\rest\behaviors\EagerLoadingBehavior;
use backendApiOauthV1\models\OauthClient;
use backendApiOauthV1\models\OauthClientSearch;

/**
 * 客户端控制器。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientController extends \backendApiOauthV1\components\ActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = OauthClient::class;

    /**
     * {@inheritdoc}
     */
    public $searchModelClass = OauthClientSearch::class;
    
    /**
     * {@inheritdoc}
     */
    public $createScenario = OauthClient::SCENARIO_INSERT;

    /**
     * {@inheritdoc}
     */
    public $updateScenario = OauthClient::SCENARIO_UPDATE;
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'dataFilter' => [
                    'class' => ActiveDataFilter::class,
                    'searchModel' => $this->searchModelClass,
                ],
                // 即时加载指定的额外资源。
                'as eagerLoadingBehavior' => [
                    'class' => EagerLoadingBehavior::class,
                ],
            ],
            // 重置标识。
            'reset-identifier' => [
                'class' => 'devzyj\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'scenario' => OauthClient::SCENARIO_RESET_IDENTIFIER,
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'on beforeProcessModel' => function ($event) {
                    $event->object->identifier = null;
                },
            ],
            // 重置密钥。
            'reset-secret' => [
                'class' => 'devzyj\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkActionAccess' => [$this, 'checkActionAccess'],
                'checkModelAccess' => [$this, 'checkModelAccess'],
                'scenario' => OauthClient::SCENARIO_RESET_SECRET,
                'notFoundMessage' => $this->notFoundMessage,
                'notFoundCode' => $this->notFoundCode,
                'on beforeProcessModel' => function ($event) {
                    $event->object->secret = null;
                },
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
        ]);
    }
}