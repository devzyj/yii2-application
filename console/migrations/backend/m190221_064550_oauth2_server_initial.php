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
        'oauth_client' => '{{%oauth_client}}',
        'oauth_client_setting' => '{{%oauth_client_setting}}',
        'oauth_scope' => '{{%oauth_scope}}',
        'oauth_scope_content' => '{{%oauth_scope_content}}',
        'admin' => '{{%admin}}',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // oauth_client_setting
        $this->createTable($this->tables['oauth_client_setting'], [
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'admin_id' => $this->integer(10)->unsigned()->notNull()->comment('管理员 ID'),
            'allowed_ips' => $this->string(255)->notNull()->defaultValue('')->comment('允许访问的 IPs'),
        ], "COMMENT='OAuth - 客户端配置表'");
        $this->addPrimaryKey('PK_oauth_client_setting_client_id', $this->tables['oauth_client_setting'], 'client_id');
        $foreignKeyName = $this->getForeignKeyName($this->tables['oauth_client_setting'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['oauth_client_setting'], 'client_id', $this->tables['oauth_client'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['oauth_client_setting'], 'admin_id');
        $this->addForeignKey($foreignKeyName, $this->tables['oauth_client_setting'], 'admin_id', $this->tables['admin'], 'id', 'CASCADE', 'CASCADE');

        // oauth_scope_content
        $this->createTable($this->tables['oauth_scope_content'], [
            'scope_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'allowed_apis' => $this->string(2000)->notNull()->defaultValue('')->comment('允许访问的 APIs'),
        ], "COMMENT='OAuth - 权限内容表'");
        $this->addPrimaryKey('PK_oauth_scope_content_scope_id', $this->tables['oauth_scope_content'], 'scope_id');
        $foreignKeyName = $this->getForeignKeyName($this->tables['oauth_scope_content'], 'scope_id');
        $this->addForeignKey($foreignKeyName, $this->tables['oauth_scope_content'], 'scope_id', $this->tables['oauth_scope'], 'id', 'CASCADE', 'CASCADE');
        
        // 初始化默认数据。
        $this->update($this->tables['oauth_client'], [
            'identifier' => 'f4c22926e400ebca',
            'secret' => '692569f364854bc130687297c770c2c0',
            'redirect_uri' => 'http://backend.application.yii2.devzyj.zyj/test/oauth-callback',
            'access_token_duration' => 86400,
        ], ['id' => 1]);
        
        $this->insert($this->tables['oauth_client_setting'], [
            'client_id' => 1,
            'admin_id' => 1,
            'allowed_ips' => '*',
        ]);

        $this->insert($this->tables['oauth_scope_content'], [
            'scope_id' => 1,
            'allowed_apis' => '*',
        ]);

        $this->insert($this->tables['oauth_scope_content'], [
            'scope_id' => 2,
            'allowed_apis' => '/v1/admins/view/',
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
