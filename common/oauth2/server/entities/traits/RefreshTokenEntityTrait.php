<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities\traits;

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
     * @var AccessTokenEntityInterface 与更新令牌关联的访问令牌。
     */
    private $_accessToken;

    /**
     * 获取与更新令牌关联的访问令牌。
     *
     * @return AccessTokenEntityInterface 访问令牌实例。
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * 设置与更新令牌关联的访问令牌。
     *
     * @param AccessTokenEntityInterface $accessToken 访问令牌实例。
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->_accessToken = $accessToken;
    }
}