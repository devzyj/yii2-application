<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;
use common\models\rbac\Client;
use common\models\rbac\Operation;
use common\models\rbac\OperationPermission;
use common\models\rbac\Permission;
use common\models\rbac\PermissionRole;
use common\models\rbac\Role;
use common\models\rbac\RoleUser;
use common\models\rbac\User;
use common\models\api\Client as ApiClient;

/**
 * Class m181120_094029_rbac_initial
 */
class m181120_094029_rbac_initial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->db = Client::getDb();
        
        parent::init();
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTables();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        /*$this->dropTable(RoleUser::tableName());
        $this->dropTable(PermissionRole::tableName());
        $this->dropTable(OperationPermission::tableName());
        $this->dropTable(User::tableName());
        $this->dropTable(Role::tableName());*/
        $this->dropTable(Permission::tableName());
        $this->dropTable(Operation::tableName());
        $this->dropTable(Client::tableName());
    }
    
    /**
     * 创建数据表。
     */
    protected function createTables()
    {
        $clientTableName = Client::tableName();
        $operationTableName = Operation::tableName();
        $permissionTableName = Permission::tableName();
        
        // client
        $this->createTable($clientTableName, [
            'id' => $this->primaryKey(10)->unsigned()->comment('客户端ID'),
            'identifier' => $this->string(20)->notNull()->unique()->comment('API客户端标识'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('客户端描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
        ], "COMMENT='RBAC - 客户端表'");

        // operation
        $this->createTable($operationTableName, [
            'id' => $this->primaryKey(10)->unsigned()->comment('操作ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端ID'),
            'code' => $this->string(255)->notNull()->comment('操作编码'),
            'name' => $this->string(50)->notNull()->comment('操作名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('操作描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('操作状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 操作表'");
        $this->createIndex('client_id_code', $operationTableName, ['client_id', 'code'], true);
        $this->createIndex('client_id_name', $operationTableName, ['client_id', 'name'], true);
        $namePrefix = 'fk_' . $this->db->getSchema()->getRawTableName($operationTableName) . '_';
        $this->addForeignKey($namePrefix . "client_id", $operationTableName, 'client_id', $clientTableName, 'id', 'CASCADE', 'CASCADE');

        // permission
        $this->createTable($permissionTableName, [
            'id' => $this->primaryKey(10)->unsigned()->comment('权限ID'),
            'client_id' => $this->integer(10)->unsigned()->notNull()->comment('客户端ID'),
            'name' => $this->string(50)->notNull()->comment('权限名称'),
            'description' => $this->string(255)->notNull()->defaultValue('')->comment('权限描述'),
            'create_time' => $this->integer(10)->unsigned()->notNull()->comment('创建时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('权限状态（0=禁用；1=可用）'),
        ], "COMMENT='RBAC - 权限表'");
        $this->createIndex('client_id_name', $permissionTableName, ['client_id', 'name'], true);
        $namePrefix = 'fk_' . $this->db->getSchema()->getRawTableName($permissionTableName) . '_';
        $this->addForeignKey($namePrefix . 'client_id', $permissionTableName, 'client_id', $clientTableName, 'id', 'CASCADE', 'CASCADE');
        
        // role TODO
        
    }
}
