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
        'oauth_client' => '{{%backend_oauth_client}}',
        'oauth_scope' => '{{%backend_oauth_scope}}',
        'oauth_client_scope' => '{{%backend_oauth_client_scope}}',
        'oauth_user' => '{{%backend_oauth_user}}',
        'oauth_user_scope' => '{{%backend_oauth_user_scope}}',
        //'oauth_operation' => '{{%backend_oauth_operation}}',
        //'oauth_scope_operation' => '{{%backend_oauth_scope_operation}}',
        
        'admin' => '{{%backend_admin}}',
        'admin_login_log' => '{{%backend_admin_login_log}}',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
        $this->insertRows();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //$this->dropTable($this->tables['oauth_scope_operation']);
        //$this->dropTable($this->tables['oauth_operation']);
        

        $this->dropTable($this->tables['oauth_user_scope']);
        $this->dropTable($this->tables['oauth_user']);
        $this->dropTable($this->tables['oauth_client_scope']);
        $this->dropTable($this->tables['oauth_scope']);
        $this->dropTable($this->tables['admin_login_log']);
        $this->dropTable($this->tables['oauth_client']);
        $this->dropTable($this->tables['admin']);
    }
    
    /**
     * 创建数据表。
     */
    protected function createTables()
    {
        $tables = $this->tables;
        $schema = $this->db->getSchema();

        // oauth_client
        $this->createTable($tables['oauth_client'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'name' => $this->string(50)->notNull()->unique()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('创建者 ID'),
            'allowed_ips' => $this->string(255)->notNull()->defaultValue('')->comment('允许访问的 IPs'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
            'identifier' => $this->string(20)->notNull()->unique()->comment('标识'),
            'secret' => $this->string(32)->notNull()->unique()->comment('密钥'),
            'grant_types' => $this->string(100)->notNull()->defaultValue('')->comment('支持的授权类型'),
            'access_token_duration' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('访问令牌的持续时间'),
            'refresh_token_duration' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('刷新令牌的持续时间'),
        ], "COMMENT='后端授权客户端表'");
        
        // oauth_scope
        $this->createTable($tables['oauth_scope'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'scope' => $this->string(100)->notNull()->unique()->comment('权限范围'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='后端授权权限范围表'");

        // oauth_client_scope
        $this->createTable($tables['oauth_client_scope'], [
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'scope_id' => $this->integer(10)->unsigned()->notNull()->comment('权限范围 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='后端授权客户端与权限范围关联表'");
        $this->addPrimaryKey('pk_client_id_scope_id', $tables['oauth_client_scope'], ['client_id', 'scope_id']);

        // oauth_user
        $this->createTable($tables['oauth_user'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='后端授权用户表'");
        
        // oauth_user_scope
        $this->createTable($tables['oauth_user_scope'], [
            'user_id' => $this->integer(10)->unsigned()->notNull()->comment('用户 ID'),
            'scope_id' => $this->integer(10)->unsigned()->notNull()->comment('权限范围 ID'),
            'is_default' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否默认（0=否；1=是）'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='后端授权用户与权限范围关联表'");
        $this->addPrimaryKey('pk_user_id_scope_id', $tables['oauth_user_scope'], ['user_id', 'scope_id']);
        
        
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

        // admin_login_log
        $this->createTable($tables['admin_login_log'], [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('管理员 ID'),
            'ip' => $this->string(50)->notNull()->comment('登录 IP'),
            'time' => $this->integer(10)->unsigned()->notNull()->comment('登录时间'),
        ], "COMMENT='后端管理员登录日志表'");
        
    }
    
    /**
     * 创建数据表索引。
     */
    protected function createIndexes()
    {
        $tables = $this->tables;
        $schema = $this->db->getSchema();
        
        // oauth_client
        $prefix = 'fk_' . $schema->getRawTableName($tables['oauth_client']) . '_';
        $this->addForeignKey($prefix . 'admin_id', $tables['oauth_client'], 'admin_id', $tables['admin'], 'id', 'RESTRICT', 'RESTRICT');

        // oauth_client_scope
        $prefix = 'fk_' . $schema->getRawTableName($tables['oauth_client_scope']) . '_';
        $this->addForeignKey($prefix . 'client_id', $tables['oauth_client_scope'], 'client_id', $tables['oauth_client'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($prefix . 'scope_id', $tables['oauth_client_scope'], 'scope_id', $tables['oauth_scope'], 'id', 'CASCADE', 'CASCADE');

        // oauth_user
        $prefix = 'fk_' . $schema->getRawTableName($tables['oauth_user']) . '_';
        $this->addForeignKey($prefix . 'id', $tables['oauth_user'], 'id', $tables['admin'], 'id', 'CASCADE', 'CASCADE');
        
        // oauth_user_scope
        $prefix = 'fk_' . $schema->getRawTableName($tables['oauth_user_scope']) . '_';
        $this->addForeignKey($prefix . 'user_id', $tables['oauth_user_scope'], 'user_id', $tables['oauth_user'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($prefix . 'scope_id', $tables['oauth_user_scope'], 'scope_id', $tables['oauth_scope'], 'id', 'CASCADE', 'CASCADE');
        
        
        // admin_login_log
        $prefix = 'fk_' . $schema->getRawTableName($tables['admin_login_log']) . '_';
        $this->addForeignKey($prefix . 'client_id', $tables['admin_login_log'], 'client_id', $tables['oauth_client'], 'id', 'CASCADE', 'CASCADE');
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
        
        // oauth_client
        $this->insert($this->tables['oauth_client'], [
            'name' => '后端管理系统',
            'description' => '在 backend 中使用的客户端',
            'admin_id' => $adminId,
            'allowed_ips' => '*',
            'create_time' => time(),
            'status' => 1,
            'identifier' => 'f4c22926e400ebca',
            'secret' => '692569f364854bc130687297c770c2c0',
            'grant_types' => ',client_credentials,password,refresh_token,',
            'access_token_duration' => 10800, // 3 hours
            'refresh_token_duration' => 2592000, // 30 days
        ]);
        $clientId = $this->db->getLastInsertID();

        // oauth_scope
        $this->insert($this->tables['oauth_scope'], [
            'scope' => 'basic',
            'description' => '基础权限',
            'create_time' => time(),
        ]);
        $scopeId = $this->db->getLastInsertID();

        // oauth_user
        $this->insert($this->tables['oauth_user'], [
            'id' => $adminId,
            'create_time' => time(),
        ]);
        $userId = $this->db->getLastInsertID();

        // oauth_user_scope
        $this->insert($this->tables['oauth_user_scope'], [
            'user_id' => $userId,
            'scope_id' => $scopeId,
            'is_default' => 1,
            'create_time' => time(),
        ]);
        
    }
}
