<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use yii\web\User;
use devjerry\yii2\oauth2\server\interfaces\OAuthLoginFormInterface;

/**
 * DemoLoginForm class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class DemoLoginForm extends \yii\base\Model implements OAuthLoginFormInterface
{
    /**
     * @var string
     */
    public $username;
    
    /**
     * @var string
     */
    public $password;
    
    /**
     * @var array
     */
    public $scopes;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password'], 'string'],
            [['scopes'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultScopes(array $scopes)
    {
        $this->scopes = $scopes;
    }
    
    /**
     * {@inheritdoc}
     */
    public function login(User $user)
    {
        if (!$this->validate()) {
            return false;
        }
        
        if (!$this->loginInternal($user)) {
            return false;
        }
        
        $this->setApproved($user);
        return true;
    }
    
    /**
     * 用户登录。
     * 
     * @param User $user
     * @return boolean
     */
    protected function loginInternal(User $user)
    {
        /* @var $userIdentity DemoAuthorizationIdentity */
        $userIdentity = DemoAuthorizationIdentity::findByUsername($this->username);
        if (!$userIdentity || !$userIdentity->validatePassword($this->password)) {
            $this->addError('password', 'Incorrect username or password.');
            return false;
        }
        
        if ($user->login($userIdentity, 300)) {
            $this->addError('password', 'User login error.');
            return false;
        }
        
        return true;
    }
    
    /**
     * 设置用户同意授权的信息。
     * 
     * @param User $user
     */
    protected function setApproved(User $user)
    {
        $userIdentity = $user->getIdentity();
        $userIdentity->setOAuthIsApproved(true);
        $userIdentity->setOAuthScopes($this->scopes);
    }
}
