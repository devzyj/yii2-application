<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\models;

/**
 * This is the model class for table "{{%api_client}}".
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Client extends \common\models\api\Client
{
    const RATE_LIMIT_CACHE_BEHAVIOR = 'rateLimitCacheBehavior';
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // 速率限制的缓存方法。
        $behaviors[self::RATE_LIMIT_CACHE_BEHAVIOR] = [
            'class' => 'devzyj\behaviors\ModelCacheBehavior',
            'baseModelCacheKey' => ['Api', 'Client', 'RateLimit'],
            'defaultDuration' => 86400, // 24 hours
        ];
        
        return $behaviors;
    }
    
    /**
     * @todo
     */
    public function getRateLimit($request, $action)
    {
        
    }

    /**
     * @todo
     */
    public function loadAllowance($request, $action)
    {
    
    }

    /**
     * @todo
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
    
    }
}
