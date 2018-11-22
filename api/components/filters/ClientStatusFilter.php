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
 * ClientStatusFilter 实现了验证客户端状态是否有效。
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
 * ClientStatusFilter 需要 [[user]] 实现 [[ClientStatusFilterInterface]]。
 * 如果 [[user]] 没有设置或没有实现 [[ClientStatusFilterInterface]]， ClientStatusFilter 将什么也不做。
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
     * @var \yii\web\Request 当前的请求。如果没有设置，将使用 `Yii::$app->getRequest()`。
     */
    public $request;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->request === null) {
            $this->request = Yii::$app->getRequest();
        }
    
        parent::init();
    }
    
    /**
     * {@inheritdoc}
     * 
     * @throws \yii\web\ForbiddenHttpException 客户端状态不可用。
     */
    public function beforeAction($action)
    {
        if (($user = Yii::$app->getUser()) && ($identity = $user->getIdentity(false))) {
            if ($identity instanceof ClientStatusFilterInterface) {
                if (!$identity->checkClientStatus($action, $this->request)) {
                    throw new ForbiddenHttpException($this->errorMessage, $this->errorCode);
                }
            }
        }
        
        return true;
    }
}
