<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

/**
 * AuthorizationCodeGrantAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationCodeGrantAction extends GrantAction
{
    /**
     * Generate credentials.
     * 
     * @return array
     */
    public function run()
    {
        return 'todo';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getGrantType()
    {
        return self::GRANT_TYPE_AUTHORIZATION_CODE;
    }
}