<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m190221_063231_initial
 */
class m190221_063231_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'admin' => '{{%admin}}',
        'admin_login_log' => '{{%admin_login_log}}',
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
        $this->dropTable($this->tables['admin']);
    }

    /**
     * 创建数据表。
     */
    protected function createTables()
    {
        // admin
        $this->createTable($this->tables['admin'], [
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
        $this->createTable($this->tables['admin_login_log'], [
            'id' => $this->bigPrimaryKey()->unsigned()->comment('ID'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('管理员 ID'),
            'ip' => $this->string(50)->notNull()->comment('登录 IP'),
            'time' => $this->integer(10)->unsigned()->notNull()->comment('登录时间'),
        ], "COMMENT='后端管理员登录日志表'");

        $foreignKeyName = $this->getForeignKeyName($this->tables['admin_login_log'], 'admin_id');
        $this->addForeignKey($foreignKeyName, $this->tables['admin_login_log'], 'admin_id', $this->tables['admin'], 'id', 'CASCADE', 'CASCADE');
        
    }

    /**
     * 获取外键名称。
     */
    protected function getForeignKeyName($table, $column)
    {
        $schema = $this->db->getSchema();
        return 'FK_' . $schema->getRawTableName($table) . '_' . $column;
    }
    
    /**
     * 插入数据。
     */
    protected function insertRows()
    {
        // admin
        $password = '123456';
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
    }
}
