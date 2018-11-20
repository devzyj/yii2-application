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
 * ClientStatusFilter 检查客户端状态。
 * 
 * 必需设置 `user` 组件中的 `$identity` 为 [[\api\components\Identity]]。
 * 如果没有设置则跳过检查。
 * 
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'clientStatusFilter' => [
 *             'class' => 'api\components\filters\ClientStatusFilter',
 *         ],
 *     ];
 * }
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientStatusFilter extends \yii\base\ActionFilter
{
    /**
     * @var string 错误信息。
     */
    public $errorMessage = 'Client is invalid.';

    /**
     * @var integer 错误编码。
     */
    public $errorCode = 0;
    
    /**
     * {@inheritdoc}
     * 
     * @throws \yii\web\ForbiddenHttpException 客户端状态不可用。
     */
    public function beforeAction($action)
    {
        if (($user = Yii::$app->getUser()) && ($identity = $user->getIdentity(false))) {
            if ($identity instanceof \api\components\Identity) {
                if (!$identity->checkClientStatus()) {
                    throw new ForbiddenHttpException($this->errorMessage, $this->errorCode);
                }
            }
        }
        
        return true;
    }
}
