<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\components;

/**
 * Key class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class Key
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string|null
     */
    protected $passphrase;

    /**
     * @param string $path
     * @param string|null $passphrase
     */
    public function __construct($path, $passphrase = null)
    {
        if (strpos($path, 'file://') !== 0) {
            $path = 'file://' . $path;
        }

        $this->path = $path;
        $this->$passphrase = $passphrase;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getPassphrase()
    {
        return $this->passphrase;
    }
}
