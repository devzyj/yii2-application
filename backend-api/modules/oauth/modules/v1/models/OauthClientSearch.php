<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiOauthV1\models;

use yii\helpers\ArrayHelper;
use backendApiRbacV1\behaviors\QueryJoinWithBehaviorInterface;
use backendApi\behaviors\VirtualAttributesBehavior;

/**
 * 查询客户端数据模型。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClientSearch extends OauthClient implements QueryJoinWithBehaviorInterface
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        // 获取全部查询属性。
        $searchAttributes = array_keys($this->searchAttributeFieldMap());
        
        // 移除自身已存在的属性。
        $virtualAttributes = array_diff($searchAttributes, $this->attributes());
        
        return ArrayHelper::merge([
            // 为模型添加虚拟属性的行为。
            'virtualAttributesBehavior' => [
                'class' => VirtualAttributesBehavior::class,
                'attributes' => $virtualAttributes,
            ],
        ], parent::behaviors());
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // self
            [['id', 'access_token_duration', 'refresh_token_duration', 'create_time', 'status'], 'integer'],
            [['name', 'description', 'identifier', 'secret', 'grant_types', 'redirect_uri'], 'string'],
        ];
    }
    
    /**
     * 查询属性和字段的映射。
     * 
     * @return array
     */
    public function searchAttributeFieldMap()
    {
        return [
            // self
            'id' => static::tableName() . '.id',
            'name' => static::tableName() . '.name',
            'description' => static::tableName() . '.description',
            'identifier' => static::tableName() . '.identifier',
            'secret' => static::tableName() . '.secret',
            'grant_types' => static::tableName() . '.grant_types',
            'redirect_uri' => static::tableName() . '.redirect_uri',
            'access_token_duration' => static::tableName() . '.access_token_duration',
            'refresh_token_duration' => static::tableName() . '.refresh_token_duration',
            'create_time' => static::tableName() . '.create_time',
            'status' => static::tableName() . '.status',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getQueryJoinWithByTables($names, $query)
    {
        return [];
    }
}
