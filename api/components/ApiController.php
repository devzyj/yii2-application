<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace api\components;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * ApiController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ApiController extends \yii\rest\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            // 验证客户端 IP 是否被允许访问。
            'clientIpFilter' => [
                'class' => 'api\components\filters\ClientIpFilter',
            ],
        ]);
    }
}