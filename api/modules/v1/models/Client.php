<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace apiV1\models;

/**
 * This is the model class for table "{{%api_client}}".
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Client extends \api\models\Client
{
    /**
     * @var string 新增数据的场景名称。
     */
    const SCENARIO_INSERT = 'insert';
    
    /**
     * @var string 更新数据的场景名称。
     */
    const SCENARIO_UPDATE = 'update';
    
    /**
     * @var string 重置ID的场景名称。
     */
    const SCENARIO_RESET_ID = 'resetId';
    
    /**
     * @var string 重置密钥的场景名称。
     */
    const SCENARIO_RESET_SECRET = 'resetSecret';
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // 默认场景。
        $scenarios = parent::scenarios();
    
        // 新增数据的场景。
        $scenarios[self::SCENARIO_INSERT] = [
            'name', 'description', 'status', 'token_expires_in', 'refresh_token_expires_in', 
            'rate_limit_count', 'rate_limit_seconds', 'allowed_ips', 'allowed_apis'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'name', 'description', 'status', 'token_expires_in', 'refresh_token_expires_in', 
            'rate_limit_count', 'rate_limit_seconds', 'allowed_ips', 'allowed_apis'
        ];
    
        // 重置ID的场景。
        $scenarios[self::SCENARIO_RESET_ID] = [];
    
        // 重置密钥的场景。
        $scenarios[self::SCENARIO_RESET_SECRET] = [];
    
        // 返回修改后的场景列表。
        return $scenarios;
    }
}
