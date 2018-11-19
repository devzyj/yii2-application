<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBinV1\components;

use Yii;
use yii\filters\RateLimitInterface;

/**
 * 访问接口的客户端标识类。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Identity extends \api\components\Identity implements RateLimitInterface
{
    /******************************* IdentityInterface *******************************/
    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var $module \apiAuthorize\Module */
        $module = Yii::$app->getModule('authorize');
        $jwt = $module->getToken()->getClientCredentials($token);
        if ($jwt && $jwt->hasClaim('client_id')) {
            $clientId = $jwt->getClaim('client_id');
            return static::findOrSetOneById($clientId);
        }
    }

    /************************************* RateLimitInterface *************************************/
    /**
     * 获取访问速率限制的缓存 KEY。
     * 
     * @return array
     */
    protected function getRateLimitCacheKey()
    {
        return ['Identity', 'RateLimit', $this->getId()];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRateLimit($request, $action)
    {
        $count = $this->rate_limit_count;
        $seconds = $this->rate_limit_seconds ? $this->rate_limit_seconds : 1;
        
        // 在 `$seconds` 秒内最多 `$count` 次的 API 调用。
        return [$count, $seconds];
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadAllowance($request, $action)
    {
        // 返回剩余的允许请求数和最后一次速率限制检查时的时间戳。
        $cache = Yii::$app->getCache();
        if ($cache) {
            $key = $this->getRateLimitCacheKey();
            return $cache->get($key);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        if (!$this->getIsSuperAdministrator()) {
            // 保存剩余的允许请求数和速率限制检查时的时间戳。
            $cache = Yii::$app->getCache();
            if ($cache) {
                $key = $this->getRateLimitCacheKey();
                $data = [$allowance, $timestamp];
            
                $rateLimit = $this->getRateLimit($request, $action);
                $duration = isset($rateLimit[1]) ? $rateLimit[1] : null;
            
                $cache->set($key, $data, $duration);
            }
        }
    }
}
