<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 更新令牌实体接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface RefreshTokenEntityInterface extends BasicTokenEntityInterface
{
    /**
     * 获取与更新令牌关联的访问令牌。
     *
     * @return AccessTokenEntityInterface 访问令牌实例。
     */
    public function getAccessToken();
    
    /**
     * 设置与更新令牌关联的访问令牌。
     * 
     * @param AccessTokenEntityInterface $accessToken 访问令牌实例。
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken);
}