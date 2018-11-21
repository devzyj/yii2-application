<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m181120_094029_rbac_initial
 */
class m181120_094029_rbac_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'client' => '{{%rbac_client}}',
        'user' => '{{%rbac_user}}',
        'role' => '{{%rbac_role}}',
        'roleUser' => '{{%rbac_role_user}}',
        'group' => '{{%rbac_group}}',
        'groupUser' => '{{%rbac_group_user}}',
        'permission' => '{{%rbac_permission}}',
        'permissionRole' => '{{%rbac_permission_role}}',
        'permissionGroup' => '{{%rbac_permission_group}}',
        'operation' => '{{%rbac_operation}}',
        'operationPermission' => '{{%rbac_operation_permission}}',
        'menu' => '{{%rbac_menu}}',
        'menuPermission' => '{{%rbac_menu_permission}}',
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
        $tables = $this->tables;

        $this->dropTable($tables['menuPermission']);
        $this->dropTable($tables['menu']);
        $this->dropTable($tables['operationPermission']);
        $this->dropTable($tables['operation']);
        $this->dropTable($tables['permissionGroup']);
        $this->dropTable($tables['permissionRole']);
        $this->dropTable($tables['permission']);
        $this->dropTable($tables['groupUser']);
        $this->dropTable($tables['group']);
        $this->dropTable($tables['roleUser']);
        $this->dropTable($tables['role']);
        $this->dropTable($tables['user']);
        $this->dropTable($tables['client']);
    }
    
    /**
     * 创建数据表。
     */
    protected function createTables()
    {
        $tables = $this->tables;
        $schema = $this->db->getSchema();
        
        // client
        $this->createTable($tables['client'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('客户端ID'),
            'name' => $this->string(50)->notNull()->unique()->comment('客户端名称'),
            'identifier' => $this->string(20)->notNull()->unique()->comment('API客户端标识'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('客户端描述'),
            'type' => $this->string(20)->notNull()->comment('客户端类型'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 客户端表'");

        // user
        $this->createTable($tables['user'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('用户ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端ID'),
            'account' => $this->string(100)->notNull()->comment('用户标识'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('用户描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 用户表'");
        $this->createIndex('client_id_account', $tables['user'], ['client_id', 'account'], true);
        $prefix = 'fk_' . $schema->getRawTableName($tables['user']) . '_';
        $this->addForeignKey($prefix . 'client_id', $tables['user'], 'client_id', $tables['client'], 'id', 'CASCADE', 'CASCADE');

        // role
        $this->createTable($tables['role'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('角色ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端ID'),
            'name' => $this->string(50)->notNull()->comment('角色名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('角色描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('角色状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 角色表'");
        $this->createIndex('client_id_name', $tables['role'], ['client_id', 'name'], true);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['role']) . '_';
        $this->addForeignKey($namePrefix . 'client_id', $tables['role'], 'client_id', $tables['client'], 'id', 'CASCADE', 'CASCADE');

        // role_user
        $this->createTable($tables['roleUser'], [
            'role_id' => $this->integer(10)->unsigned()->notNull()->comment('角色ID'),
            'user_id' => $this->integer(10)->unsigned()->notNull()->comment('用户ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 角色与用户关联表'");
        $this->addPrimaryKey('pk_role_id_user_id', $tables['roleUser'], ['role_id', 'user_id']);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['roleUser']) . '_';
        $this->addForeignKey($namePrefix . 'role_id', $tables['roleUser'], 'role_id', $tables['role'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($namePrefix . 'user_id', $tables['roleUser'], 'user_id', $tables['user'], 'id', 'CASCADE', 'CASCADE');

        // group
        $this->createTable($tables['group'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('组ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端ID'),
            'parent_id' => $this->integer(10)->unsigned()->comment('上级组ID'),
            'name' => $this->string(50)->notNull()->comment('组名称'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 组织结构表'");
        $this->createIndex('client_id_parent_id_name', $tables['group'], ['client_id', 'parent_id', 'name'], true);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['group']) . '_';
        $this->addForeignKey($namePrefix . 'client_id', $tables['group'], 'client_id', $tables['client'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($namePrefix . "parent_id", $tables['group'], 'parent_id', $tables['group'], 'id', 'CASCADE', 'CASCADE');
        
        // group_user
        $this->createTable($tables['groupUser'], [
            'group_id' => $this->integer(10)->unsigned()->notNull()->comment('组ID'),
            'user_id' => $this->integer(10)->unsigned()->notNull()->comment('用户ID'),
            'is_admin' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('组管理员（0=否；1=是）'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 组织结构与用户关联表'");
        $this->addPrimaryKey('pk_group_id_user_id', $tables['groupUser'], ['group_id', 'user_id']);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['groupUser']) . '_';
        $this->addForeignKey($namePrefix . 'role_id', $tables['groupUser'], 'group_id', $tables['group'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($namePrefix . 'user_id', $tables['groupUser'], 'user_id', $tables['user'], 'id', 'CASCADE', 'CASCADE');
        
        // permission
        $this->createTable($tables['permission'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('权限ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端ID'),
            'name' => $this->string(50)->notNull()->comment('权限名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('权限描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('权限状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 权限表'");
        $this->createIndex('client_id_name', $tables['permission'], ['client_id', 'name'], true);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['permission']) . '_';
        $this->addForeignKey($namePrefix . 'client_id', $tables['permission'], 'client_id', $tables['client'], 'id', 'CASCADE', 'CASCADE');

        // permission_role
        $this->createTable($tables['permissionRole'], [
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限ID'),
            'role_id' => $this->integer(10)->unsigned()->notNull()->comment('角色ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 权限与角色关联表'");
        $this->addPrimaryKey('pk_permission_id_role_id', $tables['permissionRole'], ['permission_id', 'role_id']);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['permissionRole']) . '_';
        $this->addForeignKey($namePrefix . 'permission_id', $tables['permissionRole'], 'permission_id', $tables['permission'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($namePrefix . 'role_id', $tables['permissionRole'], 'role_id', $tables['role'], 'id', 'CASCADE', 'CASCADE');

        // permission_group
        $this->createTable($tables['permissionGroup'], [
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限ID'),
            'group_id' => $this->integer(10)->unsigned()->notNull()->comment('组ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 权限与组织结构关联表'");
        $this->addPrimaryKey('pk_permission_id_group_id', $tables['permissionGroup'], ['permission_id', 'group_id']);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['permissionGroup']) . '_';
        $this->addForeignKey($namePrefix . 'permission_id', $tables['permissionGroup'], 'permission_id', $tables['permission'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($namePrefix . 'group_id', $tables['permissionGroup'], 'group_id', $tables['group'], 'id', 'CASCADE', 'CASCADE');
        
        // operation
        $this->createTable($tables['operation'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('操作ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端ID'),
            'code' => $this->string(255)->notNull()->comment('操作编码'),
            'name' => $this->string(50)->notNull()->comment('操作名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('操作描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('操作状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 操作表'");
        $this->createIndex('client_id_code', $tables['operation'], ['client_id', 'code'], true);
        $this->createIndex('client_id_name', $tables['operation'], ['client_id', 'name'], true);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['operation']) . '_';
        $this->addForeignKey($namePrefix . "client_id", $tables['operation'], 'client_id', $tables['client'], 'id', 'CASCADE', 'CASCADE');

        // operation_permission
        $this->createTable($tables['operationPermission'], [
            'operation_id' => $this->integer(10)->unsigned()->notNull()->comment('操作ID'),
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 操作与权限关联表'");
        $this->addPrimaryKey('pk_operation_id_permission_id', $tables['operationPermission'], ['operation_id', 'permission_id']);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['operationPermission']) . '_';
        $this->addForeignKey($namePrefix . 'operation_id', $tables['operationPermission'], 'operation_id', $tables['operation'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($namePrefix . 'permission_id', $tables['operationPermission'], 'permission_id', $tables['permission'], 'id', 'CASCADE', 'CASCADE');

        // menu
        $this->createTable($tables['menu'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('菜单ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端ID'),
            'parent_id' => $this->integer(10)->unsigned()->comment('上级菜单ID'),
            'name' => $this->string(50)->notNull()->comment('菜单名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('菜单描述'),
            'order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('菜单状态（0=禁用；1=可用）'),
            'data' => $this->text()->notNull()->comment('额外数据'),
        ], "COMMENT='RBAC - 菜单表'");
        $this->createIndex('client_id_parent_id', $tables['menu'], ['client_id', 'parent_id']);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['menu']) . '_';
        $this->addForeignKey($namePrefix . "client_id", $tables['menu'], 'client_id', $tables['client'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($namePrefix . "parent_id", $tables['menu'], 'parent_id', $tables['menu'], 'id', 'CASCADE', 'CASCADE');
        
        // menu_permission
        $this->createTable($tables['menuPermission'], [
            'menu_id' => $this->integer(10)->unsigned()->notNull()->comment('菜单ID'),
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 菜单与权限关联表'");
        $this->addPrimaryKey('pk_menu_id_permission_id', $tables['menuPermission'], ['menu_id', 'permission_id']);
        $namePrefix = 'fk_' . $schema->getRawTableName($tables['menuPermission']) . '_';
        $this->addForeignKey($namePrefix . 'menu_id', $tables['menuPermission'], 'menu_id', $tables['menu'], 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($namePrefix . 'permission_id', $tables['menuPermission'], 'permission_id', $tables['permission'], 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * 插入数据。
     */
    protected function insertRows()
    {
        $tables = $this->tables;
        
        // client
        $this->insert($tables['client'], [
            'name' => 'backend',
            'identifier' => 'f4c22926e400ebca',
            'description' => 'Backend Management System',
            'type' => 'SUPER',
            'create_time' => time(),
        ]);
        $clientId = $this->db->getLastInsertID();

        // user
        $this->insert($tables['user'], [
            'client_id' => $clientId,
            'account' => 'admin',
            'description' => 'admin',
            'create_time' => time(),
        ]);
        $userId = $this->db->getLastInsertID();
        
        // role
        $this->insert($tables['role'], [
            'client_id' => $clientId,
            'name' => 'administrator',
            'description' => 'administrator',
            'create_time' => time(),
            'status' => 1,
        ]);
        $roleId = $this->db->getLastInsertID();
        
        // role_user
        $this->insert($tables['roleUser'], [
            'role_id' => $roleId,
            'user_id' => $userId,
            'create_time' => time(),
        ]);
    }
}
