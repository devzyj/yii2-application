<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\entities\traits;

use common\oauth2\server\interfaces\AccessTokenEntityInterface;

/**
 * RefreshTokenEntityTrait
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
trait RefreshTokenEntityTrait
{
    use TokenEntityTrait;
    
    /**
     * @var AccessTokenEntityInterface
     */
    private $_accessToken;

    /**
     * 获取与更新令牌关联的访问令牌。
     *
     * @return AccessTokenEntityInterface
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }
    
    /**
     * 设置与更新令牌关联的访问令牌。
     *
     * @param AccessTokenEntityInterface $token
    */
    public function setAccessToken(AccessTokenEntityInterface $token)
    {
        $this->_accessToken = $token;
    }
}