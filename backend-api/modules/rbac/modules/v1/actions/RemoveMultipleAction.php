<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiRbacV1\actions;

use Yii;
use yii\db\ActiveRecordInterface;
use yii\db\ActiveQuery;
use devzyj\rest\BatchActionTrait;

/**
 * RemoveMultipleAction 是移除多个模型关联关系的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class RemoveMultipleAction extends \devzyj\rest\Action
{
    use BatchActionTrait;
    
    /**
     * @var string 关联名称。
     */
    public $relationName;
    
    /**
     * 移除模型关联关系。
     * 
     * @param string $id 模型主键。
     * @param string $relationids 关联模型主键，为 `null` 时，移除所有关联关系。
     * @return ActiveRecordInterface[]|null
     */
    public function run($id, $relationids = null)
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
        
        // 关联模型主键列表为 `null`，移除所有关联关系。
        if ($relationids === null) {
            // 移除所有关联关系。
            $model->unlinkAll($this->relationName, true);
        
            // 设置响应状态。
            Yii::$app->getResponse()->setStatusCode(204);
            
            // 不返回任何数据。
            return;
        } else {
            // 将多个ID转换成数组。
            $ids = $this->convertRequestIds($relationids);
            
            // 准备需要移除的关联模型列表。
            $relations = $this->prepareRelations($model, $ids);
            
            // 循环移除关联关系。
            foreach ($relations as $relation) {
                $model->unlink($this->relationName, $relation, true);
            }
            
            // 返回成功移除的关联模型。
            return $relations;
        }
    }
    
    /**
     * 准备需要移除的关联模型列表。
     * 
     * @param ActiveRecordInterface $model 模型实例。
     * @param array $relationids 关联模型主键。
     * @return ActiveRecordInterface[]
     */
    protected function prepareRelations($model, $relationids)
    {
        if (empty($relationids)) {
            return [];
        }

        $method = 'get' . ucfirst($this->relationName);
        /* @var $query ActiveQuery */
        $query = $model->{$method}();
        $query->andWhere(['id' => $relationids]);
        return $query->all();
    }
}
