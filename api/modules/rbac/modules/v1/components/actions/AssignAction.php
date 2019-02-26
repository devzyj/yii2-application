<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\actions;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

/**
 * AssignAction 是分配模型关联关系的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AssignAction extends \devzyj\rest\Action
{
    /**
     * @var string 关联名称。
     */
    public $relationName;
    
    /**
     * @var string 关联模型类名。
     */
    public $relationModelClass;
    
    /**
     * 分配模型关联关系。
     * 
     * @param string $id 模型主键。
     * @param string $relationid 关联模型主键。
     * @return \yii\db\ActiveRecordInterface 成功分配的关联模型。
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

        // 准备需要分配的关联模型。
        $relation = $this->prepareRelation($model, $relationid);
        
        // 分配模型并返回结果。
        return $this->assignRelation($model, $relation);
    }
    
    /**
     * 准备需要分配的关联模型。
     * 
     * @param \yii\db\ActiveRecordInterface $model 模型实例。
     * @param string $relationid 关联模型主键。
     * @return \yii\db\ActiveRecordInterface[]
     * @throws \yii\web\NotFoundHttpException 关联模型不存在。
     * @throws \yii\web\ForbiddenHttpException 模型与关联模型中的 `client_id` 不同，或者已经分配过。
     */
    protected function prepareRelation($model, $relationid)
    {
        // 关联模型。
        /* @var $relationModelClass \yii\db\ActiveRecordInterface */
        $relationModelClass = $this->relationModelClass;
        
        // 获取关联模型。
        $relation = $relationModelClass::findOne($relationid);
        if (empty($relation)) {
            throw new NotFoundHttpException('Relation object not found.');
        } elseif ($relation->client_id != $model->client_id) {
            throw new ForbiddenHttpException('Client not same.');
        }
        
        // 验证是否已分配。
        $method = 'get' . ucfirst($this->relationName);
        /* @var $query \yii\db\ActiveQuery */
        $query = $model->{$method}();
        $query->andWhere(['id' => $relationid]);
        if ($query->exists()) {
            throw new ForbiddenHttpException('Assigned.');
        };
        
        return $relation;
    }

    /**
     * 分配关联模型。
     * 
     * @param \yii\db\ActiveRecordInterface $model
     * @param \yii\db\ActiveRecordInterface $relation
     * @return \yii\db\ActiveRecordInterface 分配成功的关联模型。
     */
    protected function assignRelation($model, $relation)
    {
        $model->link($this->relationName, $relation, ['create_time' => time()]);
        return $relation;
    }
}
