<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m190226_053428_rbac_group_initial
 */
class m190226_053428_rbac_group_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'group' => '{{%rbac_group}}',
        'groupUser' => '{{%rbac_group_user}}',
        'permissionGroup' => '{{%rbac_permission_group}}',
        'client' => '{{%rbac_client}}',
        'user' => '{{%rbac_user}}',
        'permission' => '{{%rbac_permission}}',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // group
        $this->createTable($this->tables['group'], [
            'id' => $this->primaryKey(10)->unsigned()->comment('ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端 ID'),
            'parent_id' => $this->integer(10)->unsigned()->comment('上级 ID'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 组织结构表'");
        $this->createIndex('client_id_parent_id_name', $this->tables['group'], ['client_id', 'parent_id', 'name'], true);
        $foreignKeyName = $this->getForeignKeyName($this->tables['group'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['group'], 'client_id', $this->tables['client'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['group'], 'parent_id');
        $this->addForeignKey($foreignKeyName, $this->tables['group'], 'parent_id', $this->tables['group'], 'id', 'CASCADE', 'CASCADE');
        
        // group_user
        $this->createTable($this->tables['groupUser'], [
            'group_id' => $this->integer(10)->unsigned()->notNull()->comment('组 ID'),
            'user_id' => $this->integer(10)->unsigned()->notNull()->comment('用户 ID'),
            'is_admin' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('是否管理员（0=否；1=是）'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 组织结构与用户关联表'");
        $this->addPrimaryKey('pk_group_id_user_id', $this->tables['groupUser'], ['group_id', 'user_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['groupUser'], 'group_id');
        $this->addForeignKey($foreignKeyName, $this->tables['groupUser'], 'group_id', $this->tables['group'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['groupUser'], 'user_id');
        $this->addForeignKey($foreignKeyName, $this->tables['groupUser'], 'user_id', $this->tables['user'], 'id', 'CASCADE', 'CASCADE');
        
        // permission_group
        $this->createTable($this->tables['permissionGroup'], [
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'group_id' => $this->integer(10)->unsigned()->notNull()->comment('组 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 权限与组织结构关联表'");
        $this->addPrimaryKey('pk_permission_id_group_id', $this->tables['permissionGroup'], ['permission_id', 'group_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['permissionGroup'], 'permission_id');
        $this->addForeignKey($foreignKeyName, $this->tables['permissionGroup'], 'permission_id', $this->tables['permission'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['permissionGroup'], 'group_id');
        $this->addForeignKey($foreignKeyName, $this->tables['permissionGroup'], 'group_id', $this->tables['group'], 'id', 'CASCADE', 'CASCADE');
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
        $this->dropTable($this->tables['permissionGroup']);
        $this->dropTable($this->tables['groupUser']);
        $this->dropTable($this->tables['group']);
    }
}
