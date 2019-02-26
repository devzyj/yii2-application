<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\actions;

use Yii;
use yii\db\ActiveRecordInterface;
use yii\db\ActiveQuery;

/**
 * AssignMultipleAction 是分配多个模型关联关系的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AssignMultipleAction extends \devzyj\rest\Action
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
     * @return ActiveRecordInterface[] 成功分配关系的模型列表。
     */
    public function run($id)
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
        
        // 获取请求的参数。
        $ids = Yii::$app->getRequest()->getBodyParams();
        
        // 准备需要分配的关联模型列表。
        $relations = $this->prepareRelations($model, $ids);
        
        // 分配模型并返回结果。
        return $this->assignRelations($model, $relations);
    }
    
    /**
     * 准备需要分配的关联模型列表。
     * 
     * @param ActiveRecordInterface $model 模型实例。
     * @param array $ids 关联模型主键。
     * @return ActiveRecordInterface[]
     */
    protected function prepareRelations($model, $ids)
    {
        if (empty($ids)) {
            return [];
        }
        
        // 获取已分配的模型主键。
        $method = 'get' . ucfirst($this->relationName);
        /* @var $query ActiveQuery */
        $query = $model->{$method}();
        $query->select(['id']);
        $assignIds = $query->column();
        
        // 获取需要分配且未分配的模型主键。
        $ids = array_diff($ids, $assignIds);
        
        // 关联模型。
        /* @var $relationModelClass ActiveRecordInterface */
        $relationModelClass = $this->relationModelClass;
        
        // 查询对像。
        $query = $relationModelClass::find();
        
        // 添加查询条件。
        $query->andWhere([
            'id' => $ids,
            'client_id' => $model->client_id,
        ]);
        
        // 获取所有满足条件的模型列表。
        return $query->all();
    }

    /**
     * 分配模型。
     * 
     * @param ActiveRecordInterface $model
     * @param ActiveRecordInterface[] $relations
     * @return ActiveRecordInterface[] 分配成功的模型列表。
     */
    protected function assignRelations($model, $relations)
    {
        foreach ($relations as $relation) {
            $model->link($this->relationName, $relation, ['create_time' => time()]);
        }
        
        return $relations;
    }
}
