<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components\repositories;

use common\oauth2\server\interfaces\ClientRepositoryInterface;
use common\oauth2\server\components\entities\ClientEntity;

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
     * @return ClientEntity 客户端实例。
     */
    public function getClientEntityByCredentials($identifier, $secret)
    {
        /* @var $client ClientEntity */
        $client = ClientEntity::findOneByIdentifier($identifier);
        if ($client->secret === $secret) {
            return $client;
        }
    }
}