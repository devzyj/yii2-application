<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use yii\base\BaseObject;
use yii\web\IdentityInterface;
use devjerry\yii2\oauth2\server\interfaces\OAuthIdentityInterface;
use devjerry\yii2\oauth2\server\entities\ScopeEntity;

/**
 * DemoAuthorizationIdentity class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class DemoAuthorizationIdentity extends BaseObject implements IdentityInterface, OAuthIdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $isApproved;
    public $scopes;
    
    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {}

    /**
     * Finds user by username
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {}

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {}

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    /***************************** OAuthIdentityInterface *****************************/
    /**
     * {@inheritdoc}
     */
    public function getOAuthUserEntity()
    {
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOAuthIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * {@inheritdoc}
     */
    public function setOAuthIsApproved($value)
    {
        $this->isApproved = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getOAuthScopeEntities()
    {
        if ($this->scopes !== null) {
            return ScopeEntity::findAll(['identifier' => $this->scopes]);
        }
    }
    
    public function setOAuthScopes($value)
    {
        
    }
}
