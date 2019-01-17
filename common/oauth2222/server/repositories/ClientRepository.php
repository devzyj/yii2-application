<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories;

use common\oauth2\server\interfaces\ClientRepositoryInterface;
use common\oauth2\server\entities\ClientEntity;

/**
 * ClientRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientRepository implements ClientRepositoryInterface
{
    /**
     * {@inheritdoc}
     * 
     * @deprecated 可能不需要。最终完成时如果没有使用，会移除方法。
     */
    public function getClientEntity($identifier)
    {
        return ClientEntity::findOneByIdentifier($identifier);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getClientEntityByCredentials($identifier, $secret = null)
    {
        $client = $this->getClientEntity($identifier);
        if (empty($client)) {
            return null;
        } elseif ($secret !== null && $client->secret !== $secret) {
            return null;
        }
        
        return $client;
    }
}