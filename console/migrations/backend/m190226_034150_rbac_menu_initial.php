<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m190226_034150_rbac_menu_initial
 */
class m190226_034150_rbac_menu_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'menu' => '{{%rbac_menu}}',
        'menuPermission' => '{{%rbac_menu_permission}}',
        'client' => '{{%rbac_client}}',
        'permission' => '{{%rbac_permission}}',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // menu
        $this->createTable($this->tables['menu'], [
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
        $this->createIndex('client_id_parent_id', $this->tables['menu'], ['client_id', 'parent_id']);
        $foreignKeyName = $this->getForeignKeyName($this->tables['menu'], 'client_id');
        $this->addForeignKey($foreignKeyName, $this->tables['menu'], 'client_id', $this->tables['client'], 'id', 'CASCADE', 'CASCADE');
        $foreignKeyName = $this->getForeignKeyName($this->tables['menu'], 'parent_id');
        $this->addForeignKey($foreignKeyName, $this->tables['menu'], 'parent_id', $this->tables['menu'], 'id', 'CASCADE', 'CASCADE');
        
        // menu_permission
        $this->createTable($this->tables['menuPermission'], [
            'menu_id' => $this->integer(10)->unsigned()->notNull()->comment('菜单 ID'),
            'permission_id' => $this->integer(10)->unsigned()->notNull()->comment('权限 ID'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 菜单与权限关联表'");
        $this->addPrimaryKey('pk_menu_id_permission_id', $this->tables['menuPermission'], ['menu_id', 'permission_id']);
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
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tables['menuPermission']);
        $this->dropTable($this->tables['menu']);
    }
}
