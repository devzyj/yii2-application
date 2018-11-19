<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;
use common\models\api\Client;

/**
 * Class m181116_100816_api_client_initial
 */
class m181116_100816_api_client_initial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->db = Client::getDb();
        
        parent::init();
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 创建表。
        $this->createTable(Client::tableName(), [
            'id' => $this->string(20)->notNull()->comment('客户端ID'),
            'name' => $this->string(50)->notNull()->unique()->comment('客户端名称'),
            'secret' => $this->string(32)->notNull()->unique()->comment('客户端密钥'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('客户端描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('客户端状态（0=禁用；1=可用）'),
            'token_expires_in' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('令牌过期时间（秒）'),
            'rate_limit_count' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('速率限制次数'),
            'rate_limit_seconds' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('速率限制秒数'),
            'allowed_ips' => $this->text()->notNull()->defaultValue('')->comment('允许访问的 IPs'),
            'allowed_apis' => $this->text()->notNull()->defaultValue('')->comment('允许访问的 APIs'),
            'PRIMARY KEY (`id`)',
        ], "COMMENT='API - 客户端表'");
        
        // 插入数据。
        $this->insert(Client::tableName(), [
            'id' => Client::generateId(), // e.g. 'f4c22926e400ebca'
            'name' => 'app-backend',
            'secret' => Client::generateSecret(), // e.g. '692569f364854bc130687297c770c2c0'
            'description' => '后台管理系统',
            'create_time' => time(),
            'status' => 1,
            'token_expires_in' => 604800,
            'rate_limit_count' => 9999999,
            'rate_limit_seconds' => 1,
            'allowed_ips' => '*',
            'allowed_apis' => '*',
        ]);
        $this->insert(Client::tableName(), [
            'id' => Client::generateId(),
            'name' => 'app-frontend',
            'secret' => Client::generateSecret(),
            'description' => '前端网站系统',
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
        $this->dropTable(Client::tableName());
    }
}
