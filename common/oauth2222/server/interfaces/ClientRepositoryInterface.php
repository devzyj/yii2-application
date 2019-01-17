<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 客户端存储库接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface ClientRepositoryInterface
{
    /**
     * 获取客户端实例。
     * 
     * @param string $identifier 客户端标识。
     * @return ClientEntityInterface 客户端实例。
     * @deprecated 可能不需要。最终完成时如果没有使用，会移除方法。
     */
    public function getClientEntity($identifier);
    
    /**
     * 使用客户端认证信息，获取客户端实例。
     * 
     * @param string $identifier 客户端标识。
     * @param string|null $secret 客户端密钥。
     * @return ClientEntityInterface 客户端实例。
     */
    public function getClientEntityByCredentials($identifier, $secret = null);
}