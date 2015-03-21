<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartVariant
 * @since 2.2
 */
class JBCartVariant extends ArrayObject
{
    /**
     * Id of variant
     * @type integer
     */
    public $id = 0;

    /**
     * Array of objects elements
     * @type array
     */
    public $elements = array();

    /**
     * Hash based on simple elements and their values.
     * @type string
     */
    protected $hash;

    /**
     * @type float
     */
    protected $total = null;

    /**
     * @type float
     */
    protected $price = null;

    /**
     * @type JBCartVariantList
     */
    protected $list;

    /**
     * Class constructor
     * @param JBCartVariantList $list
     * @param array             $elements
     * @param array             $options
     */
    public function __construct($elements = array(), $options = array(), JBCartVariantList $list = null)
    {
        // set variant id
        if(isset($options['id']))
        {
            $this->setId($options['id']);
        }

        // set variant list if exists
        if ($list instanceof JBCartVariantList)
        {
            $this->setList($list);
            unset($list);
        }

        //Bind elements
        if ($elements)
        {
            $data = new AppData();
            if(isset($options['elements']))
            {
                $data->exchangeArray($options['elements']);
            }
            $this->add($elements, $data);

            unset($elements);
        }

        //set elements data
        if(isset($options))
        {
            $this->bindData($options);
        }

        unset($options);
    }

    /**
     * Get elements value
     * @param string $key
     * @param mixed $default
     * @return JBCartElementPrice|mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->elements[$key]) ? $this->elements[$key] : $default;
    }

    /**
     * @param integer        $key
     * @param ElementJBPrice $element
     * @throws JBCartVariantException
     */
    public function set($key, $element)
    {
        if (!$element instanceof JBCartElementPrice) {
            throw new JBCartVariantException('In Method: ' . __FUNCTION__ . ' values of array must be an instance of JBCartElementPrice.');
        }

        $this->elements[$key] = $element;
    }

    /**
     * @param array          $elements
     * @param AppData|array $options
     */
    public function add(array $elements, $options = array())
    {
        foreach($elements as $key => $element) {
            $this->set($key, $this->_setElement($element, $options->get($key)));
        }

        unset($options);
    }

    /**
     * Check if JBCartVariant exists.
     * @param  integer $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    /**
     * Get all elements
     * @return array
     */
    public function all()
    {
        return $this->elements;
    }

    /**
     * @param string $group Group of elements to count
     * @return int
     */
    public function count($group = 'all')
    {
        if($group == 'all')
        {
            $count = count($this->elements);
        }
        elseif($group == 'core')
        {
            $count = count($this->core());
        }
        else
        {
            $count = count($this->simple());
        }

        return $count;
    }

    /**
     * Get JBCartVariant id
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        if ($this->id != $id)
        {
            $this->id = (int)$id;
        }

        return $this;
    }

    /**
     * Return hash for variant base on elements and theirs values.
     * @return string
     */
    public function hash()
    {
        if (isset($this->hash)) {
            return $this->hash;
        }

        $this->hash = md5(serialize(array_filter(array_map(create_function('$element',
                'return JString::strlen($element->getValue(true)) > 0 && $element->isCore() == false ? $element->getValue(true) : null;'), $this->all())
        )));

        return $this->hash;
    }

    /**
     *  Clear all data
     */
    public function clear()
    {
        $this->elements = array();
        $this->total    = null;
        $this->price    = null;
        $this->hash     = null;
        $this->id       = null;
    }

    /**
     * Set link to list of variations
     * @param JBCartVariantList $list
     * @return JBCartVariant
     */
    public function setList(JBCartVariantList $list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * Check if variant is basic
     * @return bool
     */
    public function isBasic()
    {
        return $this->id === 0;
    }

    /**
     * @param string $key - element identifier
     * @param mixed  $default
     * @param bool   $toString
     * @return JBCartElementPrice|mixed
     */
    public function getValue($toString = false, $key, $default = null)
    {
        if ($element = $this->get($key))
        {
            return $element->getValue($toString);
        }

        return $default;
    }

    /**
     * @param string $id    Element identifier
     * @param mixed  $value Value to check
     * @return bool
     */
    public function hasOption($id, $value)
    {
        $element = $this->get($id);

        $element->bindData($value);
        if ($element->hasOption($element->getValue(true))) {
            return $value;
        }

        return false;
    }

    /**
     * @return array
     */
    public function core()
    {
        return array_filter($this->all(),
            create_function('$element', 'return ($element->isCore() == true && JString::strlen($element->getValue(true)) > 0);'));
    }

    /**
     * @return array
     */
    public function simple()
    {
        return array_filter($this->all(),
            create_function('$element', 'return ($element->isCore() == false && JString::strlen($element->getValue(true)) > 0);'));
    }

    /**
     * Get data from all elements
     * @return array
     */
    public function data()
    {
        return array_filter(array_map(create_function('$element',
            'return JString::strlen($element->getValue(true)) > 0 ? (array)$element->data() : null;'), $this->isBasic() ? $this->core() : $this->all()
        ));
    }

    /**
     * @param array $options
     * @return $this
     */
    public function bindData(array $options = array())
    {
        $elements = new AppData();
        if (isset($options['elements']))
        {
            $elements->exchangeArray($options['elements']);
        }

        foreach ($this->elements as $key => $element)
        {
            $this->set($key, $this->_setElement($element, $elements->get($key)));
        }

        return $this;
    }

    /**
     * Get Total price for variant
     * @return JBCartValue
     * @throws Exception
     */
    public function getTotal()
    {
        if (!is_null($this->total)) {
            return $this->total;
        }

        if (!$this->list->isOverlay) {
            $total = $this->getPrice()->minus($this->getValue(true, '_discount'), true);

            if ($this->list instanceof JBCartVariantList) {
                $total = $this->list->addModifiers($total, true);
            }
            $this->total = $total;

            return $this->total;
        }

        throw new JBCartVariantException('Variant cant calculate his own total price. Please use JBCartVariantList');
    }

    /**
     * Get price for variant
     * @return JBCartValue
     */
    public function getPrice()
    {
        if (!is_null($this->price)) {
            return $this->price;
        }

        $price = JBCart::val();
        if ($element = $this->get('_value')) {
            $price->set($element->getValue(true));
            if ($element->isModifier() && !$this->isBasic() && $this->list->isOverlay === false) {
                $price = $this->list->first()->getValue(false, '_value')->add($price);
            }
        }

        $this->price = $price;
        if ($this->list->isOverlay === false) {
            $this->price = $price->add($this->getValue(true, '_margin'), true);

            if ($this->list instanceof JBCartVariantList) {
                $this->price = $this->list->addModifiers($this->price, false);
            }
        }

        return $this->price->getClone();
    }

    /**
     * Check if item in stock
     * @param integer $quantity
     * @return bool
     */
    public function inStock($quantity)
    {
        if ($element = $this->get('_balance')) {
            if ($element->inStock($quantity)) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * If someone is accessed as a string
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @param      $name
     * @param bool $checkVars
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name);
    }

    /**
     * @param JBCartElementPrice $element
     * @param array|string       elements data
     * @return mixed
     */
    protected function _setElement($element, $data = array())
    {
        $element->setVariant($this->id);

        $_data = (array)$element->data();
        if ($this->list instanceof JBCartVariantList && !$this->isBasic())
        {
            $data['_basic'] = $this->list->first()->getValue(true, $element->identifier);
        }

        if (!empty($data))
        {
            $data = $_data + $data;
            $element->bindData($data);
        }

        return $element;
    }
}

/**
 * Class JBCartVariantException
 */
class JBCartVariantException extends AppException
{

}