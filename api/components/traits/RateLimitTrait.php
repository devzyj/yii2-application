<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components\traits;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * RateLimitTrait 实现了 [[yii\filters\RateLimitInterface]] 中的 [[loadAllowance()]] 和 [[saveAllowance()]] 方法。
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
            'ApiClientIdentityRateLimit', 
            $request->getUserIP(),
            $action->getUniqueId(),
            $this->getId()
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
        $key = $this->getRateLimitCacheKey($request, $action);
        return $this->getRateLimitCache()->get($key);
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
        $data = [$allowance, $timestamp];
        $rateLimit = $this->getRateLimit($request, $action);
        $duration = isset($rateLimit[1]) ? $rateLimit[1] : 1;

        $key = $this->getRateLimitCacheKey($request, $action);
        $this->getRateLimitCache()->set($key, $data, $duration);
    }
}
