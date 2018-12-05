<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\interfaces;

/**
 * 客户端存储接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
interface ClientRepositoryInterface
{
    /**
     * 获取客户端。
     * 
     * @param string $identifier 客户端标识。
     * @return ClientEntityInterface 客户端实例。
     */
    public function getEntity($identifier);
}