<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\components\actions\users;

use Yii;
use yii\web\ForbiddenHttpException;
use apiRbacV1\models\Operation;

/**
 * CheckOperationAction 是检查用户是否有操作使用权的动作。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CheckOperationAction extends \devzyj\rest\Action
{
    use CheckOperationTrait;
    
    /**
     * 检查用户是否有操作使用权。
     * 
     * @param string $id 模型主键。
     * @param string $code 操作编码。
     * @return \apiRbacV1\models\Operation 操作模型。
     */
    public function run($id, $code)
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
        
        // 检查并返回结果。
        return $this->checkOperation($model, $code);
    }
    
    /**
     * 检查操作。
     * 
     * @param \apiRbacV1\models\User $model 用户模型。
     * @param string $code 操作编码。
     * @return \apiRbacV1\models\Operation
     * @throws \yii\web\ForbiddenHttpException 用户没有操作使用权。
     */
    protected function checkOperation($model, $code)
    {
        /* @var $query \apiRbacV1\models\OperationQuery */
        $query = Operation::find();
        
        // 准备查询对像。
        $query = $this->prepareQuery($query, $model);
        
        // 查询条件。
        $query->andWhere([
            Operation::tableName() . '.code' => $code,
        ]);
        
        // 获取操作模型。
        $operation = $query->one();
        if (!$operation) {
            throw new ForbiddenHttpException('No access.');
        }
        
        // 返回操作模型。
        return $operation;
    }
}
