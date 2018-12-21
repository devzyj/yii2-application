<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\behaviors;

/**
 * ServerRequestBehavior 实现了 [[devzyj\oauth2\server\interfacesServerRequestInterface]] 中的方法。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ServerRequestBehavior extends \yii\base\Behavior
{
    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->owner->getBodyParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $_SERVER;
    }
}