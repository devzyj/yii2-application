<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\actions;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * RemoveAction 是移除模型关联关系的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RemoveAction extends \devzyj\rest\Action
{
    /**
     * @var string 关联名称。
     */
    public $relationName;
    
    /**
     * 移除模型关联关系。
     * 
     * @param string $id 模型主键。
     * @param string $relationid 关联模型主键。
     */
    public function run($id, $relationid)
    {
        // 检查动作权限。
        if ($this->checkActionAccess) {
            call_user_func($this->checkActionAccess, $this);
        }

        // 准备模型。
        $model = $this->prepareModel($id);
        
        // 检查模型权限。
        if ($this->checkModelAccess) {
            call_user_func($this->checkModelAccess, $model, $this);
        }

        // 准备需要移除的关联模型。
        $relation = $this->prepareRelation($model, $relationid);
        
        // 移除关联关系。
        $model->unlink($this->relationName, $relation, true);
        
        // 设置响应状态。
        Yii::$app->getResponse()->setStatusCode(204);
        
        // 不返回任何数据。
        return;
    }
    
    /**
     * 准备需要移除的关联模型。
     * 
     * @param \yii\db\ActiveRecordInterface $model 模型实例。
     * @param string $relationid 关联模型主键。
     * @return \yii\db\ActiveRecordInterface[]
     */
    protected function prepareRelation($model, $relationid)
    {
        $method = 'get' . ucfirst($this->relationName);
        /* @var $query \yii\db\ActiveQuery */
        $query = $model->{$method}();
        $query->andWhere(['id' => $relationid]);
        $relation = $query->one();
        
        if (empty($relation)) {
            throw new ForbiddenHttpException('Unassigned.');
        }
        
        return $relation;
    }
}
