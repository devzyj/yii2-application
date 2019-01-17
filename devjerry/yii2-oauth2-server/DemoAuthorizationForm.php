<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use yii\web\User;
use devjerry\yii2\oauth2\server\interfaces\OAuthAuthorizationFormInterface;

/**
 * DemoAuthorizationForm class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class DemoAuthorizationForm extends DemoLoginForm implements OAuthAuthorizationFormInterface
{
    /**
     * @var integer 已登录用户授权模式。
     */
    const MODE_USER = 0;
    
    /**
     * @var integer 用户登录授权模式。
     */
    const MODE_LOGIN = 1;
    
    /**
     * @var integer 授权模式。
     */
    public $mode;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'when' => function ($model) {
                return $model->mode === self::MODE_LOGIN;
            }],
            [['username', 'password'], 'string', 'when' => function ($model) {
                return $model->mode === self::MODE_LOGIN;
            }],
            [['scopes', 'mode'], 'required'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function authorization(User $user)
    {
        if (!$this->validate()) {
            return false;
        }
        
        if ($this->mode === self::MODE_LOGIN) {
            
        }
        
        $this->setApproved($user);
        return true;
    }
}
