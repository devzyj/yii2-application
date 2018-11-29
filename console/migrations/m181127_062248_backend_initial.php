<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m181127_062248_backend_initial
 */
class m181127_062248_backend_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'client' => '{{%backend_client}}',
        'admin' => '{{%backend_admin}}',
        'admin_login_log' => '{{%backend_admin_login_log}}',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTables();
        $this->insertRows();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tables['admin_login_log']);
        $this->dropTable($this->tables['client']);
        $this->dropTable($this->tables['admin']);
    }
    
    /**
     * 创建数据表。
     */
    protected function createTables()
    {
        $tables = $this->tables;
        $schema = $this->db->getSchema();

        // admin
        $this->createTable($tables['admin'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'username' => $this->string(20)->notNull()->unique()->comment('用户名'),
            'password_hash' => $this->string(32)->notNull()->comment('密码 hash 值'),
            'hash_code' => $this->string(6)->notNull()->comment('密码 hash 附加值'),
            'nickname' => $this->string(20)->notNull()->unique()->comment('昵称'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
            'email' => $this->string(255)->notNull()->defaultValue('')->comment('邮箱地址'),
            'mobile' => $this->string(20)->notNull()->defaultValue('')->comment('手机号码'),
            'avatar' => $this->string(255)->notNull()->defaultValue('')->comment('头像信息'),
            'allowed_ips' => $this->string(255)->notNull()->defaultValue('')->comment('允许登录的 IPs'),
        ], "COMMENT='后端管理员表'");

        // client
        $this->createTable($tables['client'], [
            'id' => $this->string(20)->notNull()->comment('ID'),
            'name' => $this->string(50)->notNull()->unique()->comment('名称'),
            'secret' => $this->string(32)->notNull()->unique()->comment('密钥'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('创建者 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
            'allowed_ips' => $this->string(255)->notNull()->defaultValue('')->comment('允许访问的 IPs'),
            'allowed_apis' => $this->string(255)->notNull()->defaultValue('')->comment('允许访问的 APIs'),
            'access_token_duration' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('访问令牌的持续时间'),
            'refresh_token_duration' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('刷新令牌的持续时间'),
            'scope' => $this->string(1000)->notNull()->defaultValue('')->comment('权限范围'),
        ], "COMMENT='后端授权客户端表'");
        $this->addPrimaryKey('pk_id', $tables['client'], 'id');
        $prefix = 'fk_' . $schema->getRawTableName($tables['client']) . '_';
        $this->addForeignKey($prefix . 'admin_id', $tables['client'], 'admin_id', $tables['admin'], 'id', 'RESTRICT', 'RESTRICT');
        
        // admin_login_log
        $this->createTable($tables['admin_login_log'], [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('ID'),
            'client_id' => $this->string(20)->notNull()->comment('客户端 ID'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('管理员 ID'),
            'ip' => $this->string(50)->notNull()->comment('登录 IP'),
            'time' => $this->integer(10)->unsigned()->notNull()->comment('登录时间'),
        ], "COMMENT='后端管理员登录日志表'");
        $prefix = 'fk_' . $schema->getRawTableName($tables['admin_login_log']) . '_';
        $this->addForeignKey($prefix . 'client_id', $tables['admin_login_log'], 'client_id', $tables['client'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($prefix . 'admin_id', $tables['admin_login_log'], 'admin_id', $tables['admin'], 'id', 'CASCADE', 'CASCADE');
        
    }
    
    /**
     * 插入数据。
     */
    protected function insertRows()
    {
        // admin
        $password = '111111';
        $this->insert($this->tables['admin'], [
            'username' => 'admin',
            'password_hash' => md5($password . '135790'),
            'hash_code' => '135790',
            'nickname' => '后台管理员',
            'create_time' => time(),
            'status' => 1,
            'email' => 'admin@example.com',
            'mobile' => '13123456789',
            'avatar' => '',
            'allowed_ips' => '*',
        ]);
        $adminId = $this->db->getLastInsertID();
        
        // client
        $this->insert($this->tables['client'], [
            'id' => 'f4c22926e400ebca',
            'name' => '后端管理系统',
            'secret' => '692569f364854bc130687297c770c2c0',
            'description' => '在 backend 中使用的客户端',
            'admin_id' => $adminId,
            'create_time' => time(),
            'status' => 1,
            'allowed_ips' => '*',
            'allowed_apis' => '*',
            'access_token_duration' => 10800, // 3 hours
            'refresh_token_duration' => 2592000, // 30 days
        ]);
        
    }
}
