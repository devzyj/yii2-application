<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m999999_000000_demo_data_initial
 * 
 * 只在开发测试时使用。
 */
class m999999_000000_demo_data_initial extends Migration
{
    /**
     * @var array 全部数据表名。
     */
    protected $tables = [
        'oauth_client' => '{{%oauth_client}}',
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
        // update rows: oauth_client
        $this->update($this->tables['oauth_client'], [
            'redirect_uri' => 'http://backend.application.yii2.devzyj.zyj/test/oauth-callback',
            'access_token_duration' => 86400,
        ], ['id' => 1]);
        
        // insert rbac rows.
        $this->insertRbacRows();
    }
    
    /**
     * insert rbac rows.
     */
    protected function insertRbacRows()
    {
        // insert rows: rbac_role
        $this->insert($this->tables['rbac_role'], [
            'client_id' => 1,
            'name' => 'administrator',
            'description' => '超级管理员组',
            'create_time' => time(),
            'status' => 1,
        ]);

        // insert rows: rbac_role_user
        $this->insert($this->tables['rbac_role_user'], [
            'role_id' => 1,
            'user_id' => 1,
            'create_time' => time(),
        ]);

        // insert rows: rbac_permission
        $rows = [
            'clients' => ['name' => 'RBAC-客户端管理', 'description' => 'RBAC-客户端-全部操作'],
            'users' => ['name' => 'RBAC-用户管理', 'description' => 'RBAC-用户-全部操作'],
            'roles' => ['name' => 'RBAC-角色管理', 'description' => 'RBAC-角色-全部操作'],
            'permissions' => ['name' => 'RBAC-权限管理', 'description' => 'RBAC-权限-全部操作'],
            'operations' => ['name' => 'RBAC-操作管理', 'description' => 'RBAC-操作-全部操作'],
        ];
        
        foreach ($rows as $key => $row) {
            $this->insert($this->tables['rbac_permission'], [
                'client_id' => 1,
                'name' => $row['name'],
                'description' => $row['description'],
                'create_time' => time(),
                'status' => 1,
            ]);
            $id = $this->db->getLastInsertID();
        
            $this->insert($this->tables['rbac_permission_role'], [
                'permission_id' => $id,
                'role_id' => 1,
                'create_time' => time(),
            ]);
        
            $this->insertRbacOperationRows($id, $key);
        }
    }

    /**
     * insert rbac operation rows.
     * 
     * @param integer $permissionId
     * @param string $type
     */
    protected function insertRbacOperationRows($permissionId, $type)
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
            $this->insert($this->tables['rbac_operation'], [
                'client_id' => 1,
                'code' => $row['code'],
                'name' => $row['name'],
                'description' => $row['description'],
                'create_time' => time(),
                'status' => 1,
                'data' => '',
            ]);
            $id = $this->db->getLastInsertID();
            
            $this->insert($this->tables['rbac_operation_permission'], [
                'operation_id' => $id,
                'permission_id' => $permissionId,
                'create_time' => time(),
            ]);
        }
    }
}
