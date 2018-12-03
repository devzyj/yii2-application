<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
 
use yii\db\Migration;

/**
 * Class m181203_032806_oauth2_server_initial
 */
class m181203_032806_oauth2_server_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'oauth_client' => '{{%oauth_client}}',
        'oauth_scope' => '{{%oauth_scope}}',
        'oauth_client_scope' => '{{%oauth_client_scope}}',
        'oauth_user_scope' => '{{%oauth_user_scope}}',
        
        'oauth_access_token' => '{{%oauth_access_token}}',
        'oauth_refresh_token' => '{{%oauth_refresh_token}}',
        'oauth_auth_code' => '{{%oauth_auth_code}}',
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
        $this->dropTable($this->tables['oauth_scope']);
        $this->dropTable($this->tables['oauth_client']);
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
            'description' => $this->string(255)->notNull()->unique()->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'identifier' => $this->string(20)->notNull()->unique()->comment('标识'),
            'secret' => $this->string(32)->notNull()->unique()->comment('密钥'),
            'grant_types' => $this->string(100)->notNull()->comment('授权类型'),
            'redirect_uri' => $this->string(255)->notNull()->comment('回调地址'),
            'private_key' => $this->string(255)->notNull()->defaultValue('')->comment('私钥路径'),
            'private_key_password' => $this->string(255)->notNull()->defaultValue('')->comment('私钥密码'),
            'encryption_key' => $this->string(255)->notNull()->defaultValue('')->comment('加密密钥'),
        ], "COMMENT='授权客户端表'");
        
        // oauth_scope
        $this->createTable($tables['oauth_scope'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'identifier' => $this->string(20)->notNull()->unique()->comment('标识'),
            'description' => $this->string(255)->notNull()->unique()->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ]);
    }
    
    /**
     * 创建数据表索引。
     */
    protected function createIndexes()
    {
        
    }
    
    /**
     * 插入数据。
     */
    protected function insertRows()
    {
        
    }
}
