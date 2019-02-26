<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\behaviors;

use yii\base\InvalidArgumentException;

/**
 * VirtualAttributesBehavior 是为模型添加虚拟属性的行为。
 * 
 * For example:
 * 
 * ```php
 * // Demo.php
 * class Demo extends \yii\base\Model
 * {
 *     public function behaviors()
 *     {
 *         return [
 *             'virtualAttributesBehavior' => [
 *                 'class' => 'backendApi\behaviors\VirtualAttributesBehavior',
 *                 'attributes' => ['name', 'nickname'],
 *             ],
 *         ];
 *     }
 * }
 * 
 * 
 * // Usage
 * $model = new Demo();
 * $model->name = 'Name';
 * $model->nickname = 'Nickname';
 * echo $model->virtualAttributes;
 * echo $model->getVirtualAttributes();
 * ```
 * 
 * @property array $virtualAttributes Virtual attribute values (name => value).
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class VirtualAttributesBehavior extends \yii\base\Behavior
{
    /**
     * @var array the list of virtual attribute names.
     */
    public $attributes = [];

    /**
     * @var array virtual attribute values indexed by attribute names.
     */
    private $_attributes = [];

    /**
     * Returns virtual attribute values.
     * 
     * @param array $names list of virtual attributes whose value needs to be returned.
     * Defaults to null, meaning all virtual attributes listed in [[$attributes]] will be returned.
     * If it is an array, only the virtual attributes in the array will be returned.
     * @param array $except list of virtual attributes whose value should NOT be returned.
     * @return array virtual attribute values (name => value).
     */
    public function getVirtualAttributes($names = null, $except = [])
    {
        $values = [];
        
        if ($names === null) {
            $names = $this->$attributes;
        }
        
        foreach ($names as $name) {
            $values[$name] = $this->$name;
        }
        
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }
    
    /**
     * Returns a value indicating whether the model has a virtual attribute with the specified name.
     * 
     * @param string $name the name of the virtual attribute.
     * @return bool whether the model has a virtual attribute with the specified name.
     */
    public function hasVirtualAttribute($name)
    {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes, true);
    }

    /**
     * Returns the named virtual attribute value.
     * 
     * @param string $name the virtual attribute name.
     * @return mixed the virtual attribute value. `null` if the virtual attribute is not set or does not exist.
     * @see hasVirtualAttribute()
     */
    public function getVirtualAttribute($name)
    {
        return isset($this->_attributes[$name]) ? $this->_attributes[$name] : null;
    }

    /**
     * Sets the named virtual attribute value.
     * 
     * @param string $name the virtual attribute name.
     * @param mixed $value the virtual attribute value.
     * @throws InvalidArgumentException if the named virtual attribute does not exist.
     * @see hasVirtualAttribute()
     */
    public function setVirtualAttribute($name, $value)
    {
        if ($this->hasVirtualAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            throw new InvalidArgumentException(get_class($this->owner) . ' has no virtual attribute named "' . $name . '".');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $this->hasVirtualAttribute($name) ? true : parent::canGetProperty($name, $checkVars);
    }

    /**
     * {@inheritdoc}
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $this->hasVirtualAttribute($name) ? true : parent::canGetProperty($name, $checkVars);
    }
    
    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        if (isset($this->_attributes[$name]) || array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }
        
        if ($this->hasVirtualAttribute($name)) {
            return null;
        }
        
        return parent::__get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        if ($this->hasVirtualAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($name)
    {
        try {
            return $this->__get($name) !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __unset($name)
    {
        if ($this->hasVirtualAttribute($name)) {
            unset($this->_attributes[$name]);
        } else {
            parent::__unset($name);
        }
    }
}