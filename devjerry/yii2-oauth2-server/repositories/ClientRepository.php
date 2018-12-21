<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\repositories;

use devzyj\oauth2\server\interfaces\ClientRepositoryInterface;
use devjerry\yii2\oauth2\server\entities\ClientEntity;

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
     */
    public function getClientEntityByCredentials($identifier, $secret = null)
    {
        $client = ClientEntity::findOneByIdentifier($identifier);
        if (empty($client)) {
            return null;
        } elseif ($secret !== null && $client->secret !== $secret) {
            return null;
        }
        
        return $client;
    }
}