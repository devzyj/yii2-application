<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiCgiBinV1\components\actions\clients;

use yii\web\ServerErrorHttpException;

/**
 * DeleteCacheAction 是删除客户端缓存的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class DeleteCacheAction extends \devzyj\rest\DeleteAction
{
    /**
     * 删除模型缓存。
     * 
     * @param \apiCgiBinV1\models\Client $model 需要删除缓存的模型实例。
     * @return boolean 是否删除成功。
     * @throws \yii\web\ServerErrorHttpException 删除缓存失败并且错误未知。
     */
    protected function deleteModel($model)
    {
        // 删除缓存。
        if ($model->deleteCache() === false) {
            throw new ServerErrorHttpException('Failed to delete the object cache for unknown reason.');
        }
    }
}
