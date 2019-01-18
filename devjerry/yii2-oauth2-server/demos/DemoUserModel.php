<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\demos;

use devjerry\yii2\oauth2\server\entities\ScopeEntity;

/**
 * DemoUserModel class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class DemoUserModel extends \yii\base\BaseObject
{
    /**
     * @var integer 用户ID。
     */
    public $id;
    /**
     * @var string 用户名。
     */
    public $username;
    /**
     * @var string 用户密码。
     */
    public $password;
    /**
     * @var string[] 用户权限。
     */
    public $scopes;
    /**
     * @var string[] 用户默认权限。
     */
    public $defaultScopes;

    /**
     * @var array 测试用户列表。
     */
    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'scopes' => [1, 2, 3],
            'defaultScopes' => [1, 2],
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'scopes' => [2, 3],
            'defaultScopes' => [3],
        ],
    ];
    
    /**
     * 通过用户ID，查找用户。
     * 
     * @param integer $id
     * @return static|null
     */
    public static function findById($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * 通过用户名，查找用户。
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }
    
        return null;
    }

    /**
     * 验证用户密码。
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
    
    /**
     * 获取用户的全部权限。
     *
     * @return ScopeEntity[]
     */
    public function getScopes()
    {
        return ScopeEntity::findAll($this->scopes);
    }

    /**
     * 获取用户的全部默认权限。
     *
     * @return ScopeEntity[]
     */
    public function getDefaultScopes()
    {
        return ScopeEntity::findAll($this->defaultScopes);
    }
}