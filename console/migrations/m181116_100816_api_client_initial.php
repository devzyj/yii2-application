<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m181116_100816_api_client_initial
 */
abstract class m181116_100816_api_client_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'client' => '{{%api_client}}',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tables = $this->tables;
        
        // 创建表。
        $this->createTable($tables['client'], [
            'id' => $this->string(20)->notNull()->comment('客户端ID'),
            'name' => $this->string(50)->notNull()->unique()->comment('客户端名称'),
            'secret' => $this->string(32)->notNull()->unique()->comment('客户端密钥'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('客户端描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('客户端状态（0=禁用；1=可用）'),
            'token_expires_in' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('令牌的过期时间（秒）'),
            'refresh_token_expires_in' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('刷新令牌的过期时间（秒）'),
            'rate_limit_count' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('速率限制次数'),
            'rate_limit_seconds' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('速率限制秒数'),
            'allowed_ips' => $this->text()->notNull()->defaultValue('')->comment('允许访问的 IPs'),
            'allowed_apis' => $this->text()->notNull()->defaultValue('')->comment('允许访问的 APIs'),
        ], "COMMENT='API - 客户端表'");
        $this->addPrimaryKey('pk_id', $tables['client'], 'id');
        
        // 插入数据。
        $this->insert($tables['client'], [
            'id' => 'f4c22926e400ebca',
            'name' => 'backend',
            'secret' => '692569f364854bc130687297c770c2c0',
            'description' => 'Backend Management System',
            'create_time' => time(),
            'status' => 1,
            'token_expires_in' => 10800, // 3 hours
            'refresh_token_expires_in' => 2592000, // 30 days
            'rate_limit_count' => 999999,
            'rate_limit_seconds' => 1,
            'allowed_ips' => '*',
            'allowed_apis' => '*',
        ]);
        $this->insert($tables['client'], [
            'id' => 'c515b193487c3556',
            'name' => 'frontend',
            'secret' => '673b0438a0201fbed38a4dfaa32ddee9',
            'description' => 'Frontend Website System',
            'create_time' => time(),
            'status' => 1,
            'token_expires_in' => 3600, // 1 hours
            'refresh_token_expires_in' => 2592000, // 30 days
            'rate_limit_count' => 999999,
            'rate_limit_seconds' => 1,
            'allowed_ips' => '*',
            'allowed_apis' => '*',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $tables = $this->tables;
        
        $this->dropTable($tables['client']);
    }
}
