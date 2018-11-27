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
        'admin' => '{{%backend_admin}}',
        'admin_login_log' => '{{%backend_admin_login_log}}',
        'admin_email' => '{{%backend_admin_email}}',
        'admin_mobile' => '{{%backend_admin_mobile}}',
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
        $this->dropTable($this->tables['admin_mobile']);
        $this->dropTable($this->tables['admin_email']);
        $this->dropTable($this->tables['admin_login_log']);
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
            'id' => $this->primaryKey(10)->unsigned()->comment('管理员ID'),
            'username' => $this->string(20)->notNull()->unique()->comment('用户名'),
            'password_hash' => $this->string(32)->notNull()->defaultValue('')->comment('密码 HASH 值'),
            'hash_code' => $this->string(10)->notNull()->defaultValue('')->comment('HASH 附加值'),
            'nickname' => $this->string(20)->notNull()->unique()->comment('昵称'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
            'avatar' => $this->text()->notNull()->comment('头像信息'),
            'allowed_ips' => $this->text()->notNull()->comment('允许登录的 IPs'),
        ], "COMMENT='BACKEND - 管理员表'");
        
        // admin_login_log
        $this->createTable($tables['admin_login_log'], [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('日志ID'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('管理员ID'),
            'ip' => $this->bigInteger()->notNull()->comment('登录IP'),
            'time' => $this->integer(10)->unsigned()->notNull()->comment('登录时间'),
        ]);
        $prefix = 'fk_' . $schema->getRawTableName($tables['admin_login_log']) . '_';
        $this->addForeignKey($prefix . 'admin_id', $tables['admin_login_log'], 'admin_id', $tables['admin'], 'id', 'CASCADE', 'CASCADE');
        
        // admin_email
        $this->createTable($tables['admin_email'], [
            'email' => $this->string(100)->notNull()->comment('邮箱地址'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('管理员ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ]);
        $this->addPrimaryKey('pk_email', $tables['admin_email'], 'email');
        $prefix = 'fk_' . $schema->getRawTableName($tables['admin_email']) . '_';
        $this->addForeignKey($prefix . 'admin_id', $tables['admin_email'], 'admin_id', $tables['admin'], 'id', 'CASCADE', 'CASCADE');
        
        // admin_mobile
        $this->createTable($tables['admin_mobile'], [
            'mobile' => $this->string(20)->notNull()->comment('手机号码'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('管理员ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ]);
        $this->addPrimaryKey('pk_mobile', $tables['admin_mobile'], 'mobile');
        $prefix = 'fk_' . $schema->getRawTableName($tables['admin_mobile']) . '_';
        $this->addForeignKey($prefix . 'admin_id', $tables['admin_mobile'], 'admin_id', $tables['admin'], 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * 插入数据。
     */
    protected function insertRows()
    {
        $password = '123456';
        $hashCode = '6938';

        // admin
        $this->insert($this->tables['admin'], [
            'username' => 'admin',
            'password_hash' => md5($password.$hashCode),
            'hash_code' => $hashCode,
            'nickname' => '后台管理员',
            'create_time' => time(),
            'status' => 1,
            'avatar' => '',
            'allowed_ips' => '*',
        ]);
        $adminId = $this->db->getLastInsertID();

        // admin_email
        $this->insert($this->tables['admin_email'], [
            'email' => 'admin@example.com',
            'admin_id' => $adminId,
            'create_time' => time(),
        ]);

        // admin_mobile
        $this->insert($this->tables['admin_mobile'], [
            'mobile' => '13123456789',
            'admin_id' => $adminId,
            'create_time' => time(),
        ]);
    }
}
