<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\traits;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * RateLimitTrait 实现了 [[yii\filters\RateLimitInterface]] 中的 [[loadAllowance()]] 和 [[saveAllowance()]] 方法。
 * 
 * 注意：必须设置了 `Yii::$app->cache` 才能生效。
 * 
 * @see \yii\filters\RateLimitInterface
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait RateLimitTrait
{
    /**
     * 获取速率限制的缓存组件。
     * 
     * @return \yii\caching\CacheInterface
     */
    protected function getRateLimitCache()
    {
        return Yii::$app->getCache();
    }
    
    /**
     * 获取速率限制的缓存 KEY。
     * 
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @param array $params 附加的参数
     * @return array
     */
    protected function getRateLimitCacheKey($request, $action, $params = [])
    {
        return ArrayHelper::merge([
            'class' => __CLASS__, 
            'identity' => $this->getId(),
            'userIP' => $request->getUserIP(),
            'actionId' => $action->getUniqueId(),
        ], $params);
    }
    
    /**
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the number of allowed requests,
     * and the second element is the corresponding UNIX timestamp.
    */
    public function loadAllowance($request, $action)
    {
        $cache = $this->getRateLimitCache();
        if ($cache) {
            $key = $this->getRateLimitCacheKey($request, $action);
            return $cache->get($key);
        }
        
        return [9999999, time()];
    }
    
    /**
     * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @param int $allowance the number of allowed requests remaining.
     * @param int $timestamp the current timestamp.
    */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $cache = $this->getRateLimitCache();
        if ($cache) {
            $data = [$allowance, $timestamp];
            $rateLimit = $this->getRateLimit($request, $action);
            $duration = isset($rateLimit[1]) ? $rateLimit[1] : 1;
            
            $key = $this->getRateLimitCacheKey($request, $action);
            $cache->set($key, $data, $duration);
        }
        
    }
}
