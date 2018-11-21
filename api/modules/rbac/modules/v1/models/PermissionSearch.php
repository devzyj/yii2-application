<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiRbacV1\models;

use yii\helpers\ArrayHelper;

/**
 * 查询权限数据模型。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class PermissionSearch extends Permission implements SearchModelInterface
{
    use SearchModelTrait;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        // 模型的验证规则。
        $rules = [
            [$this->attributes(), 'safe'],
        ];
    
        // 关联模型的验证规则。
        $relationAttributes = $this->searchRelationAttributes();
        if ($relationAttributes) {
            $rules[] = [$relationAttributes, 'safe'];
        }
    
        // 返回验证规则。
        return $rules;
    }

    /**
     * 查询时关联模型的属性与字段的映射。
     *
     * @return array
     */
    public function searchRelationAttributeMap()
    {
        return ArrayHelper::merge(
            $this->clientAttributeFieldMap(),
            $this->operationsAttributeFieldMap(),
            $this->rolesAttributeFieldMap(),
            $this->usersAttributeFieldMap()
        );
    }
    
    /**
     * 关联模型 [[Client]] 属性与字段的映射。
     * 
     * @return array
     */
    public function clientAttributeFieldMap()
    {
        /* @var $model ClientSearch */
        $model = ClientSearch::instance();
        $prefix = self::EXTRA_FIELDS_CLIENT . '_';
        $attributeMap = $model->searchAttributeFieldMap($prefix);
        unset($attributeMap[$prefix . 'id']);
        
        return $attributeMap;
    }
    
    /**
     * 关联模型 [[Operation]] 属性与字段的映射。
     * 
     * @return array
     */
    public function operationsAttributeFieldMap()
    {
        /* @var $model OperationSearch */
        $model = OperationSearch::instance();
        $prefix = self::EXTRA_FIELDS_OPERATIONS . '_';
        $attributeMap = $model->searchAttributeFieldMap($prefix);
        
        return $attributeMap;
    }
    
    /**
     * 关联模型 [[Role]] 属性与字段的映射。
     * 
     * @return array
     */
    public function rolesAttributeFieldMap()
    {
        /* @var $model RoleSearch */
        $model = RoleSearch::instance();
        $prefix = self::EXTRA_FIELDS_ROLES . '_';
        $attributeMap = $model->searchAttributeFieldMap($prefix);
        
        return $attributeMap;
    }
    
    /**
     * 关联模型 [[User]] 属性与字段的映射。
     * 
     * @return array
     */
    public function usersAttributeFieldMap()
    {
        /* @var $model UserSearch */
        $model = UserSearch::instance();
        $prefix = self::EXTRA_FIELDS_USERS . '_';
        $attributeMap = $model->searchAttributeFieldMap($prefix);
        
        return $attributeMap;
    }
}
