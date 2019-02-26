<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m190221_064550_oauth2_server_initial
 */
class m190221_064550_oauth2_server_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'oauth_client_setting' => '{{%oauth_client_setting}}',
        'oauth_scope_content' => '{{%oauth_scope_content}}',
        'oauth_client' => '{{%oauth_client}}',
        'oauth_scope' => '{{%oauth_scope}}',
        'oauth_client_scope' => '{{%oauth_client_scope}}',
        'admin' => '{{%admin}}',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // create table: oauth_client_setting
        $this->createTable($this->tables['oauth_client_setting'], [
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('管理员 ID'),
            'allowed_ips' => $this->string(255)->notNull()->defaultValue('')->comment('允许访问的 IPs（多个使用空隔符分隔）'),
        ], "COMMENT='OAuth - 客户端配置表'");
        $this->addPrimaryKey('PK_oauth_client_setting_client_id', $this->tables['oauth_client_setting'], 'client_id');
        $foreignKeyName = $this->getForeignKeyName($this->tables['oauth_client_setting'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['oauth_client_setting'], 'client_id', $this->tables['oauth_client'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['oauth_client_setting'], 'admin_id');
        $this->addForeignKey($foreignKeyName, $this->tables['oauth_client_setting'], 'admin_id', $this->tables['admin'], 'id', 'CASCADE', 'CASCADE');

        // create table: oauth_scope_content
        $this->createTable($this->tables['oauth_scope_content'], [
            'scope_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'allowed_apis' => $this->string(2000)->notNull()->defaultValue('')->comment('允许访问的 APIs（多个使用空隔符分隔）'),
        ], "COMMENT='OAuth - 权限内容表'");
        $this->addPrimaryKey('PK_oauth_scope_content_scope_id', $this->tables['oauth_scope_content'], 'scope_id');
        $foreignKeyName = $this->getForeignKeyName($this->tables['oauth_scope_content'], 'scope_id');
        $this->addForeignKey($foreignKeyName, $this->tables['oauth_scope_content'], 'scope_id', $this->tables['oauth_scope'], 'id', 'CASCADE', 'CASCADE');
        
        
        // insert rows: oauth_client
        $this->insert($this->tables['oauth_client'], [
            'name' => '后台管理系统',
            'description' => '后台管理系统',
            'identifier' => 'f4c22926e400ebca',
            'secret' => '692569f364854bc130687297c770c2c0',
            'grant_types' => 'authorization_code implicit password client_credentials refresh_token',
            'redirect_uri' => '',
            'access_token_duration' => 3600, // 1 hours
            'refresh_token_duration' => 2592000, // 30 days
            'create_time' => time(),
            'status' => 1,
        ]);

        // insert rows: oauth_scope
        $this->insert($this->tables['oauth_scope'], [
            'identifier' => 'any',
            'name' => '全部权限',
            'description' => '全部权限',
            'create_time' => time(),
        ]);
        
        // insert rows: oauth_client_scope
        $this->insert($this->tables['oauth_client_scope'], [
            'client_id' => 1,
            'scope_id' => 1,
            'is_default' => 1,
            'create_time' => time(),
        ]);

        // insert rows: oauth_client_setting
        $this->insert($this->tables['oauth_client_setting'], [
            'client_id' => 1,
            'admin_id' => 1,
            'allowed_ips' => '*',
        ]);

        // insert rows: oauth_scope_content
        $this->insert($this->tables['oauth_scope_content'], [
            'scope_id' => 1,
            'allowed_apis' => '*',
        ]);
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
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tables['oauth_client_setting']);
        $this->dropTable($this->tables['oauth_scope_content']);
    }
}
