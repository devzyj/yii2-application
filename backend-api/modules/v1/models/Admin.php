<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiV1\models;

/**
 * This is the model class for table "{{%admin}}".
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Admin extends \backendApi\models\Admin
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
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = parent::fields();
        
        unset($fields['password_hash'], $fields['hash_code']);
        
        return $fields;
    }
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // 默认场景。
        $scenarios = parent::scenarios();
    
        // 新增数据的场景。
        $scenarios[self::SCENARIO_INSERT] = [
            'username', 'password', 'nickname', 'status', 'email', 'mobile', 'avatar', 'allowed_ips'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'username', 'password', 'nickname', 'status', 'email', 'mobile', 'avatar', 'allowed_ips'
        ];
    
        // 返回修改后的场景列表。
        return $scenarios;
    }
}
