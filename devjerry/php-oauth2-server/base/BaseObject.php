<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\base;

/**
 * BaseObject 是实现了属性特性的基类。
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class BaseObject
{
    /**
     * Constructor.
     * 
     * 配置对像，并且调用 [[init()]] 方法。
     * 
     * @param array $config 用于配置对象属性的 `键->值` 对。
     */
    public function __construct($config = [])
    {
        $this->configure($config);
        $this->init();
    }
    
    /**
     * 配置对像。
     * 
     * @param array $config 配置对象属性的 `键->值` 对。
     */
    public function configure($config = [])
    {
        if (!empty($config)) {
            foreach ($config as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    /**
     * 初始化对像。
     */
    public function init()
    {}

    /**
     * PHP 魔术方法。获取对像的属性值。
     * 
     * @param string $name 属性名称。
     * @return mixed 属性值。
     * @throws \BadMethodCallException 如果方法未定义，或者是只写属性。
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new \BadMethodCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        }

        throw new \BadMethodCallException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * PHP 魔术方法。设置对像的属性值。
     *
     * @param string $name 属性名称。
     * @param mixed $value 属性值。
     * @throws \BadMethodCallException 如果方法未定义，或者是只读属性。
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new \BadMethodCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        }
        
        throw new \BadMethodCallException('Setting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * PHP 魔术方法。检查属性是否已设置。
     * 
     * @param string $name 属性名称。
     * @return boolean
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    /**
     * PHP 魔术方法。设置对像的属性为 `null`。
     * 
     * @param string $name 属性名称。
     * @throws \BadMethodCallException 如果是只读属性。
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new \BadMethodCallException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }
}
