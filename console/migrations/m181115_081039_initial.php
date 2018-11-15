<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m181115_081039_initial
 */
class m181115_081039_initial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(10)->unsigned()->comment('客户端ID'),
            'name' => $this->string(50)->notNull()->unique()->comment('客户端名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('客户端描述'),
            'identifier' => $this->string(20)->notNull()->unique()->comment('客户端标识'),
            'secret' => $this->string(32)->notNull()->unique()->comment('客户端密钥'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('客户端状态（0=禁用；1=可用）'),
            'token_expires_in' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('令牌过期时间（秒）'),
            'rate_limit_count' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('速率限制次数'),
            'rate_limit_seconds' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('速率限制秒数'),
            'allowed_ips' => $this->text()->notNull()->defaultValue('')->comment('允许访问的 IPs'),
            'allowed_apis' => $this->text()->notNull()->defaultValue('')->comment('允许访问的 APIs'),
        ], "COMMENT='RESTful API 客户端表'");
        
        $this->insert('{{%client}}', [
            'name' => 'administrator',
            'description' => '超级管理员',
            'identifier' => 'f4c22926e400ebca',
            'secret' => '692569f364854bc130687297c770c2c0',
            'create_time' => time(),
            'status' => 1,
            'token_expires_in' => 604800,
            'rate_limit_count' => 9999999,
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
        $this->dropTable($this->db->tablePrefix.'client');
    }
}
