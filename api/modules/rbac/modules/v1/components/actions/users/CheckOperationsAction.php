<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\actions\users;

use Yii;
use devzyj\rest\BatchActionTrait;
use apiRbacV1\models\Operation;

/**
 * CheckOperationsAction 是检查用户是否有多个操作使用权的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CheckOperationsAction extends \devzyj\rest\Action
{
    use BatchActionTrait;
    use CheckActionTrait;
    
    /**
     * 检查用户是否有操作使用权。
     * 
     * @param string $id 模型主键。
     * @param string $codes 操作编码。
     * @return \apiRbacV1\models\Operation[] 操作模型。
     */
    public function run($id, $codes)
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
        
        // 转换字符串型式的多个操作编码为数组类型。
        $codes = $this->convertRequestIds($codes);
        
        // 检查并返回结果。
        return $this->checkOperations($model, $codes);
    }
    
    /**
     * 检查操作。
     * 
     * @param \apiRbacV1\models\User $model 用户模型。
     * @param array $codes 操作编码。
     * @return \apiRbacV1\models\Operation[]
     */
    protected function checkOperations($model, $codes)
    {
        /* @var $query \apiRbacV1\models\OperationQuery */
        $query = Operation::find();
        
        // 准备查询对像。
        $query = $this->prepareQuery($query, $model);
        
        /* @var $modelClass \apiRbacV1\models\Operation */
        $modelClass = $query->modelClass;
        
        // 查询条件。
        $query->andWhere([
            $modelClass::tableName() . '.code' => $codes,
        ]);

        // 以 `code` 索引结果。
        $query->indexBy('code');
        
        // 获取并返回操作模型。
        return $query->all();
    }
}
