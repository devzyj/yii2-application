<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use yii\base\InvalidConfigException;
use yii\web\User;

/**
 * AuthorizationAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationAction extends \yii\base\Action
{
    /**
     * @var User 授权用户。
     */
    public $user;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if ($this->user === null) {
            throw new InvalidConfigException('The `user` property must be set.');
        }
    }
    
    /**
     * 用户确认授权。
     */
    public function run()
    {
        var_dump('authorization action.');
    }
}