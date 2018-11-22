<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\filters;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * ClientFilter 实现了验证 RBAC 客户端是否有效。
 * 
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'clientFilter' => [
 *             'class' => 'apiRbacV1\components\filters\ClientFilter',
 *         ],
 *     ];
 * }
 * ```
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientFilter extends \yii\base\ActionFilter
{
    /**
     * @var string 错误信息。
     */
    public $errorMessage = 'Rbac client is invalid.';

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
     * @throws \yii\web\ForbiddenHttpException 客户端不可用。
     */
    public function beforeAction($action)
    {
        if (($user = Yii::$app->getUser()) && ($identity = $user->getIdentity(false))) {
            if ($identity instanceof \apiRbacV1\components\Identity) {
                if (!$identity->getRbacClient()) {
                    throw new ForbiddenHttpException($this->errorMessage, $this->errorCode);
                }
            }
        }
        
        return true;
    }
}
