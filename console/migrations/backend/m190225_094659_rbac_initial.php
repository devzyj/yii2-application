<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m190225_094659_rbac_initial
 */
class m190225_094659_rbac_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'rbac_client' => '{{%rbac_client}}',
        'rbac_user' => '{{%rbac_user}}',
        'rbac_role' => '{{%rbac_role}}',
        'rbac_role_user' => '{{%rbac_role_user}}',
        'rbac_permission' => '{{%rbac_permission}}',
        'rbac_permission_role' => '{{%rbac_permission_role}}',
        'rbac_operation' => '{{%rbac_operation}}',
        'rbac_operation_permission' => '{{%rbac_operation_permission}}',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // create table: rbac_client
        $this->createTable($this->tables['rbac_client'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'identifier' => $this->string(255)->notNull()->unique()->comment('授权客户端标识'),
            'name' => $this->string(50)->notNull()->unique()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'type' => $this->string(20)->notNull()->comment('类型'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 客户端表'");

        // create table: rbac_user
        $this->createTable($this->tables['rbac_user'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'identifier' => $this->string(255)->notNull()->comment('用户标识'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 用户表'");
        $this->createIndex('client_id_identifier', $this->tables['rbac_user'], ['client_id', 'identifier'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_user'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_user'], 'client_id', $this->tables['rbac_client'], 'id', 'CASCADE', 'CASCADE');
        
        // create table: rbac_role
        $this->createTable($this->tables['rbac_role'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 角色表'");
        $this->createIndex('client_id_name', $this->tables['rbac_role'], ['client_id', 'name'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_role'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_role'], 'client_id', $this->tables['rbac_client'], 'id', 'CASCADE', 'CASCADE');
        
        // create table: rbac_role_user
        $this->createTable($this->tables['rbac_role_user'], [
            'role_id' => $this->integer(10)->unsigned()->notNull()->comment('角色 ID'),
            'user_id' => $this->integer(10)->unsigned()->notNull()->comment('用户 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 角色与用户关联表'");
        $this->addPrimaryKey('PK_role_id_user_id', $this->tables['rbac_role_user'], ['role_id', 'user_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_role_user'], 'role_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_role_user'], 'role_id', $this->tables['rbac_role'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_role_user'], 'user_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_role_user'], 'user_id', $this->tables['rbac_user'], 'id', 'CASCADE', 'CASCADE');
        
        // create table: rbac_permission
        $this->createTable($this->tables['rbac_permission'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 权限表'");
        $this->createIndex('client_id_name', $this->tables['rbac_permission'], ['client_id', 'name'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_permission'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_permission'], 'client_id', $this->tables['rbac_client'], 'id', 'CASCADE', 'CASCADE');

        // create table: rbac_permission_role
        $this->createTable($this->tables['rbac_permission_role'], [
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'role_id' => $this->integer(10)->unsigned()->notNull()->comment('角色 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 权限与角色关联表'");
        $this->addPrimaryKey('pk_permission_id_role_id', $this->tables['rbac_permission_role'], ['permission_id', 'role_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_permission_role'], 'permission_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_permission_role'], 'permission_id', $this->tables['rbac_permission'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_permission_role'], 'role_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_permission_role'], 'role_id', $this->tables['rbac_role'], 'id', 'CASCADE', 'CASCADE');

        // create table: rbac_operation
        $this->createTable($this->tables['rbac_operation'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'code' => $this->string(255)->notNull()->comment('编码'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
            'data' => $this->text()->notNull()->comment('额外数据'),
        ], "COMMENT='RBAC - 操作表'");
        $this->createIndex('client_id_code', $this->tables['rbac_operation'], ['client_id', 'code'], true);
        $this->createIndex('client_id_name', $this->tables['rbac_operation'], ['client_id', 'name'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_operation'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_operation'], 'client_id', $this->tables['rbac_client'], 'id', 'CASCADE', 'CASCADE');

        // create table: rbac_operation_permission
        $this->createTable($this->tables['rbac_operation_permission'], [
            'operation_id' => $this->integer(10)->unsigned()->notNull()->comment('操作 ID'),
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 操作与权限关联表'");
        $this->addPrimaryKey('pk_operation_id_permission_id', $this->tables['rbac_operation_permission'], ['operation_id', 'permission_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_operation_permission'], 'operation_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_operation_permission'], 'operation_id', $this->tables['rbac_operation'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['rbac_operation_permission'], 'permission_id');
        $this->addForeignKey($foreignKeyName, $this->tables['rbac_operation_permission'], 'permission_id', $this->tables['rbac_permission'], 'id', 'CASCADE', 'CASCADE');
        

        // insert rows: rbac_client
        $this->insert($this->tables['rbac_client'], [
            'identifier' => 1,
            'name' => '后台管理系统',
            'description' => '关联 `授权客户端` 中 id = 1 的记录',
            'type' => 'MANAGER',
            'create_time' => time(),
        ]);

        // insert rows: rbac_user
        $this->insert($this->tables['rbac_user'], [
            'client_id' => 1,
            'identifier' => 1,
            'description' => '关联 `后台管理员` 中 id = 1 的记录',
            'create_time' => time(),
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
        $this->dropTable($this->tables['rbac_operation_permission']);
        $this->dropTable($this->tables['rbac_operation']);
        $this->dropTable($this->tables['rbac_permission_role']);
        $this->dropTable($this->tables['rbac_permission']);
        $this->dropTable($this->tables['rbac_role_user']);
        $this->dropTable($this->tables['rbac_role']);
        $this->dropTable($this->tables['rbac_user']);
        $this->dropTable($this->tables['rbac_client']);
    }
}
