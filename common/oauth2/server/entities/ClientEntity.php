<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ClientTrait;

/**
 * ClientEntity class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientEntity implements ClientEntityInterface
{
    use EntityTrait, ClientTrait;
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setRedirectUri($uri)
    {
        $this->redirectUri = $uri;
    }
}