<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\models;

use Yii;
use yii\web\User;

/**
 * AuthorizationForm class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationForm extends \yii\base\Model
{
    public $username;
    public $password;
    public $scopes;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'on' => 'login'],
            [['username', 'password'], 'string'],
            [['scopes'], 'required'],
        ];
    }
    
    /**
     * 用户登录。
     * 
     * @param User $user
     * @return boolean
     */
    public function login($user)
    {
        if ($this->validate()) {
            /* @var $userIdentity AuthorizationIdentity */
            $userIdentity = AuthorizationIdentity::findByUsername($this->username);
            if (!$userIdentity || !$userIdentity->validatePassword($this->password)) {
                $this->addError('password', 'Incorrect username or password.');
                return false;
            }

            if ($user->login($userIdentity, 60)) {
                $userIdentity->setOAuthIsApproved(true);
                $userIdentity->setOAuthScopes($this->scopes);
                return true;
            }
        }
        
        return false;
    }
}
