<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components\filters;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * ClientIpsFilter 检查客户端允许访问的 IPs。
 * 
 * 必需设置 `user` 组件中的 `$identity` 为 [[\api\components\Identity]]。
 * 如果没有设置则跳过检查。
 * 
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'clientIpsFilter' => [
 *             'class' => 'api\components\filters\ClientIpsFilter',
 *         ],
 *     ];
 * }
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientIpsFilter extends \yii\base\ActionFilter
{
    /**
     * @var string 错误信息。
     */
    public $errorMessage = 'Client IP address limit.';
    
    /**
     * {@inheritdoc}
     * 
     * @throws \yii\web\ForbiddenHttpException 客户端访问的 IP 不被允许。
     */
    public function beforeAction($action)
    {
        if (($user = Yii::$app->getUser()) && ($identity = $user->getIdentity(false))) {
            if ($identity instanceof \api\components\Identity) {
                if (!$identity->checkClientIPs(Yii::$app->getRequest()->getUserIP())) {
                    throw new ForbiddenHttpException($this->errorMessage);
                }
            }
        }
        
        return true;
    }
}
