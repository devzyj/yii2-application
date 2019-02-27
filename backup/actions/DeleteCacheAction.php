<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backup\actions;

use yii\web\ServerErrorHttpException;

/**
 * DeleteCacheAction 删除缓存的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class DeleteCacheAction extends \devzyj\rest\DeleteAction
{
    /**
     * @var string 删除模型缓存失败时的错误信息。
     */
    public $failedMessage = 'Failed to delete the object cache for unknown reason.';

    /**
     * @var integer 删除模型缓存失败时的错误编码。
     */
    public $failedCode = 0;

    /**
     * @var string 跳过删除模型缓存时的错误信息。
     */
    public $skippedMessage = 'Skipped delete the object cache for unknown reason.';
    
    /**
     * @var integer 跳过删除模型缓存时的错误编码。
     */
    public $skippedCode = 0;
    
    /**
     * 删除模型缓存。
     * 
     * @param object $model 需要删除缓存的模型实例。模型需要实现 [[deleteCache()]] 方法。
     * @return boolean 是否删除成功。
     * @throws ServerErrorHttpException 删除缓存失败并且错误未知。
     */
    protected function deleteModel($model)
    {
        // 删除缓存。
        if ($model->deleteCache() === false) {
            throw new ServerErrorHttpException($this->failedMessage, $this->failedCode);
        }
    }
}
