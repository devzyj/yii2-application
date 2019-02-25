<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
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
        $this->dropTable($this->tables['menuPermission']);
        $this->dropTable($this->tables['menu']);
        $this->dropTable($this->tables['operationPermission']);
        $this->dropTable($this->tables['operation']);
        $this->dropTable($this->tables['permissionGroup']);
        $this->dropTable($this->tables['permissionRole']);
        $this->dropTable($this->tables['permission']);
        $this->dropTable($this->tables['groupUser']);
        $this->dropTable($this->tables['group']);
        $this->dropTable($this->tables['roleUser']);
        $this->dropTable($this->tables['role']);
        $this->dropTable($this->tables['user']);
        $this->dropTable($this->tables['client']);
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
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'identifier' => $this->string(20)->notNull()->unique()->comment('授权客户端标识'),
            'name' => $this->string(50)->notNull()->unique()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'type' => $this->string(20)->notNull()->comment('类型'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 客户端表'");

        // user
        $this->createTable($tables['user'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'identifier' => $this->string(255)->notNull()->comment('用户标识'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 用户表'");
        $this->createIndex('client_id_identifier', $tables['user'], ['client_id', 'identifier'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['user'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['user'], 'client_id', $this->tables['client'], 'id', 'CASCADE', 'CASCADE');
        
        // role
        $this->createTable($tables['role'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 角色表'");
        $this->createIndex('client_id_name', $tables['role'], ['client_id', 'name'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['role'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['role'], 'client_id', $this->tables['client'], 'id', 'CASCADE', 'CASCADE');
        
        // role_user
        $this->createTable($tables['roleUser'], [
            'role_id' => $this->integer(10)->unsigned()->notNull()->comment('角色 ID'),
            'user_id' => $this->integer(10)->unsigned()->notNull()->comment('用户 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 角色与用户关联表'");
        $this->addPrimaryKey('PK_role_id_user_id', $tables['roleUser'], ['role_id', 'user_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['roleUser'], 'role_id');
        $this->addForeignKey($foreignKeyName, $this->tables['roleUser'], 'role_id', $this->tables['role'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['roleUser'], 'user_id');
        $this->addForeignKey($foreignKeyName, $this->tables['roleUser'], 'user_id', $this->tables['user'], 'id', 'CASCADE', 'CASCADE');
        
        // group
        $this->createTable($tables['group'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'parent_id' => $this->integer(10)->unsigned()->comment('上级 ID'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 组织结构表'");
        $this->createIndex('client_id_parent_id_name', $tables['group'], ['client_id', 'parent_id', 'name'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['group'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['group'], 'client_id', $this->tables['client'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['group'], 'parent_id');
        $this->addForeignKey($foreignKeyName, $this->tables['group'], 'parent_id', $this->tables['group'], 'id', 'CASCADE', 'CASCADE');
        
        // group_user
        $this->createTable($tables['groupUser'], [
            'group_id' => $this->integer(10)->unsigned()->notNull()->comment('组 ID'),
            'user_id' => $this->integer(10)->unsigned()->notNull()->comment('用户 ID'),
            'is_admin' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否管理员（0=否；1=是）'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 组织结构与用户关联表'");
        $this->addPrimaryKey('pk_group_id_user_id', $tables['groupUser'], ['group_id', 'user_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['groupUser'], 'group_id');
        $this->addForeignKey($foreignKeyName, $this->tables['groupUser'], 'group_id', $this->tables['group'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['groupUser'], 'user_id');
        $this->addForeignKey($foreignKeyName, $this->tables['groupUser'], 'user_id', $this->tables['user'], 'id', 'CASCADE', 'CASCADE');
        
        // permission
        $this->createTable($tables['permission'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 权限表'");
        $this->createIndex('client_id_name', $tables['permission'], ['client_id', 'name'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['permission'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['permission'], 'client_id', $this->tables['client'], 'id', 'CASCADE', 'CASCADE');

        // permission_role
        $this->createTable($tables['permissionRole'], [
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'role_id' => $this->integer(10)->unsigned()->notNull()->comment('角色 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 权限与角色关联表'");
        $this->addPrimaryKey('pk_permission_id_role_id', $tables['permissionRole'], ['permission_id', 'role_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['permissionRole'], 'permission_id');
        $this->addForeignKey($foreignKeyName, $this->tables['permissionRole'], 'permission_id', $this->tables['permission'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['permissionRole'], 'role_id');
        $this->addForeignKey($foreignKeyName, $this->tables['permissionRole'], 'role_id', $this->tables['role'], 'id', 'CASCADE', 'CASCADE');

        // permission_group
        $this->createTable($tables['permissionGroup'], [
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'group_id' => $this->integer(10)->unsigned()->notNull()->comment('组 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 权限与组织结构关联表'");
        $this->addPrimaryKey('pk_permission_id_group_id', $tables['permissionGroup'], ['permission_id', 'group_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['permissionGroup'], 'permission_id');
        $this->addForeignKey($foreignKeyName, $this->tables['permissionGroup'], 'permission_id', $this->tables['permission'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['permissionGroup'], 'group_id');
        $this->addForeignKey($foreignKeyName, $this->tables['permissionGroup'], 'group_id', $this->tables['group'], 'id', 'CASCADE', 'CASCADE');
        
        // operation
        $this->createTable($tables['operation'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'code' => $this->string(255)->notNull()->comment('编码'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
            'data' => $this->text()->notNull()->comment('额外数据'),
        ], "COMMENT='RBAC - 操作表'");
        $this->createIndex('client_id_code', $tables['operation'], ['client_id', 'code'], true);
        $this->createIndex('client_id_name', $tables['operation'], ['client_id', 'name'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['operation'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['operation'], 'client_id', $this->tables['client'], 'id', 'CASCADE', 'CASCADE');

        // operation_permission
        $this->createTable($tables['operationPermission'], [
            'operation_id' => $this->integer(10)->unsigned()->notNull()->comment('操作 ID'),
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 操作与权限关联表'");
        $this->addPrimaryKey('pk_operation_id_permission_id', $tables['operationPermission'], ['operation_id', 'permission_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['operationPermission'], 'operation_id');
        $this->addForeignKey($foreignKeyName, $this->tables['operationPermission'], 'operation_id', $this->tables['operation'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['operationPermission'], 'permission_id');
        $this->addForeignKey($foreignKeyName, $this->tables['operationPermission'], 'permission_id', $this->tables['permission'], 'id', 'CASCADE', 'CASCADE');

        // menu
        $this->createTable($tables['menu'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'parent_id' => $this->integer(10)->unsigned()->comment('上级 ID'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('描述'),
            'order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('状态（0=禁用；1=可用）'),
            'data' => $this->text()->notNull()->comment('额外数据'),
        ], "COMMENT='RBAC - 菜单表'");
        $this->createIndex('client_id_parent_id', $tables['menu'], ['client_id', 'parent_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['menu'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['menu'], 'client_id', $this->tables['client'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['menu'], 'parent_id');
        $this->addForeignKey($foreignKeyName, $this->tables['menu'], 'parent_id', $this->tables['menu'], 'id', 'CASCADE', 'CASCADE');
        
        // menu_permission
        $this->createTable($tables['menuPermission'], [
            'menu_id' => $this->integer(10)->unsigned()->notNull()->comment('菜单 ID'),
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 菜单与权限关联表'");
        $this->addPrimaryKey('pk_menu_id_permission_id', $tables['menuPermission'], ['menu_id', 'permission_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['menuPermission'], 'menu_id');
        $this->addForeignKey($foreignKeyName, $this->tables['menuPermission'], 'menu_id', $this->tables['menu'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['menuPermission'], 'permission_id');
        $this->addForeignKey($foreignKeyName, $this->tables['menuPermission'], 'permission_id', $this->tables['permission'], 'id', 'CASCADE', 'CASCADE');
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
        $this->insertClientRows();
    }
    
    /**
     * insert client rows.
     */
    protected function insertClientRows()
    {
        $this->insert($this->tables['client'], [
            'name' => 'backend',
            'identifier' => 'f4c22926e400ebca',
            'description' => '后台管理系统',
            'type' => 'MANAGER',
            'create_time' => time(),
        ]);
        
        $this->insertUserRows($this->db->getLastInsertID());
    }

    /**
     * insert user rows.
     * 
     * @param integer $clientId
     */
    protected function insertUserRows($clientId)
    {
        $this->insert($this->tables['user'], [
            'client_id' => $clientId,
            'identifier' => 'admin',
            'description' => '后台管理员',
            'create_time' => time(),
        ]);
        
        $this->insertRoleRows($clientId, $this->db->getLastInsertID());
    }

    /**
     * insert role rows.
     * 
     * @param integer $clientId
     * @param integer $userId
     */
    protected function insertRoleRows($clientId, $userId)
    {
        $this->insert($this->tables['role'], [
            'client_id' => $clientId,
            'name' => 'administrator',
            'description' => '管理员-全部权限',
            'create_time' => time(),
            'status' => 1,
        ]);
        $id = $this->db->getLastInsertID();
        
        $this->insert($this->tables['roleUser'], [
            'role_id' => $id,
            'user_id' => $userId,
            'create_time' => time(),
        ]);
        
        $this->insertPermissionRows($clientId, $id);
    }

    /**
     * insert permission rows.
     * 
     * @param integer $clientId
     * @param integer $roleId
     */
    protected function insertPermissionRows($clientId, $roleId)
    {
        $rows = [
            'clients' => ['name' => 'RBAC-客户端管理', 'description' => 'RBAC-客户端-全部操作'],
            'users' => ['name' => 'RBAC-用户管理', 'description' => 'RBAC-用户-全部操作'],
            'roles' => ['name' => 'RBAC-角色管理', 'description' => 'RBAC-角色-全部操作'],
            'permissions' => ['name' => 'RBAC-权限管理', 'description' => 'RBAC-权限-全部操作'],
            'operations' => ['name' => 'RBAC-操作管理', 'description' => 'RBAC-操作-全部操作'],
        ];
        
        foreach ($rows as $key => $row) {
            $this->insert($this->tables['permission'], [
                'client_id' => $clientId,
                'name' => $row['name'],
                'description' => $row['description'],
                'create_time' => time(),
                'status' => 1,
            ]);
            $id = $this->db->getLastInsertID();
            
            $this->insert($this->tables['permissionRole'], [
                'permission_id' => $id,
                'role_id' => $roleId,
                'create_time' => time(),
            ]);
            
            $this->insertOperationRows($clientId, $id, $key);
        }
    }

    /**
     * insert operation rows.
     * 
     * @param integer $clientId
     * @param integer $permissionId
     * @param string $type
     */
    protected function insertOperationRows($clientId, $permissionId, $type)
    {
        $rows = [
            'clients' => [
                ['code' => 'rbac_client_index', 'name' => 'RBAC-客户端-首页', 'description' => 'RBAC-客户端首页'],
                ['code' => 'rbac_client_view', 'name' => 'RBAC-客户端-详细', 'description' => 'RBAC-客户端详细'],
                ['code' => 'rbac_client_create', 'name' => 'RBAC-客户端-新增', 'description' => 'RBAC-新增客户端'],
                ['code' => 'rbac_client_update', 'name' => 'RBAC-客户端-更新', 'description' => 'RBAC-更新客户端'],
                ['code' => 'rbac_client_delete', 'name' => 'RBAC-客户端-删除', 'description' => 'RBAC-删除客户端'],
            ],
            'users' => [
                ['code' => 'rbac_user_index', 'name' => 'RBAC-用户-首页', 'description' => 'RBAC-用户首页'],
                ['code' => 'rbac_user_view', 'name' => 'RBAC-用户-详细', 'description' => 'RBAC-用户详细'],
                ['code' => 'rbac_user_create', 'name' => 'RBAC-用户-新增', 'description' => 'RBAC-新增用户'],
                ['code' => 'rbac_user_update', 'name' => 'RBAC-用户-更新', 'description' => 'RBAC-更新用户'],
                ['code' => 'rbac_user_delete', 'name' => 'RBAC-用户-删除', 'description' => 'RBAC-删除用户'],
            ],
            'roles' => [
                ['code' => 'rbac_role_index', 'name' => 'RBAC-角色-首页', 'description' => 'RBAC-角色首页'],
                ['code' => 'rbac_role_view', 'name' => 'RBAC-角色-详细', 'description' => 'RBAC-角色详细'],
                ['code' => 'rbac_role_create', 'name' => 'RBAC-角色-新增', 'description' => 'RBAC-新增角色'],
                ['code' => 'rbac_role_update', 'name' => 'RBAC-角色-更新', 'description' => 'RBAC-更新角色'],
                ['code' => 'rbac_role_delete', 'name' => 'RBAC-角色-删除', 'description' => 'RBAC-删除角色'],
            ],
            'permissions' => [
                ['code' => 'rbac_permission_index', 'name' => 'RBAC-权限-首页', 'description' => 'RBAC-权限首页'],
                ['code' => 'rbac_permission_view', 'name' => 'RBAC-权限-详细', 'description' => 'RBAC-权限详细'],
                ['code' => 'rbac_permission_create', 'name' => 'RBAC-权限-新增', 'description' => 'RBAC-新增权限'],
                ['code' => 'rbac_permission_update', 'name' => 'RBAC-权限-更新', 'description' => 'RBAC-更新权限'],
                ['code' => 'rbac_permission_delete', 'name' => 'RBAC-权限-删除', 'description' => 'RBAC-删除权限'],
            ],
            'operations' => [
                ['code' => 'rbac_operation_index', 'name' => 'RBAC-操作-首页', 'description' => 'RBAC-操作首页'],
                ['code' => 'rbac_operation_view', 'name' => 'RBAC-操作-详细', 'description' => 'RBAC-操作详细'],
                ['code' => 'rbac_operation_create', 'name' => 'RBAC-操作-新增', 'description' => 'RBAC-新增操作'],
                ['code' => 'rbac_operation_update', 'name' => 'RBAC-操作-更新', 'description' => 'RBAC-更新操作'],
                ['code' => 'rbac_operation_delete', 'name' => 'RBAC-操作-删除', 'description' => 'RBAC-删除操作'],
            ],
        ];
        
        foreach ($rows[$type] as $row) {
            $this->insert($this->tables['operation'], [
                'client_id' => $clientId,
                'code' => $row['code'],
                'name' => $row['name'],
                'description' => $row['description'],
                'create_time' => time(),
                'status' => 1,
                'data' => '',
            ]);
            $id = $this->db->getLastInsertID();
            
            $this->insert($this->tables['operationPermission'], [
                'operation_id' => $id,
                'permission_id' => $permissionId,
                'create_time' => time(),
            ]);
        }
    }
}
