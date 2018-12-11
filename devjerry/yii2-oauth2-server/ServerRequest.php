<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server;

use Yii;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;

/**
 * ServerRequest class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ServerRequest extends \yii\web\Request implements ServerRequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return $this->getHeaders()->get($name, [], false);
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->getBodyParams();
    }
}