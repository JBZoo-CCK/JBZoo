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
    public $id;

    /**
     * Array of objects elements
     * @type array
     */
    public $elements = array();

    /**
     * @type JBCartValue
     */
    protected $total = null;

    /**
     * @type JBCartValue
     */
    protected $price = null;

    /**
     * @type JBCartVariantList
     */
    protected $list;

    /**
     * Empty object to set defaults
     * @type JBCartValue
     */
    private $value;

    /**
     * Class constructor
     * @param JBCartVariantList $list
     * @param array             $elements
     * @param array             $options
     */
    public function __construct($elements = array(), $options = array(), JBCartVariantList $list = null)
    {
        $this->value = JBCart::val();

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
            $this->setData($options);
        }

        unset($options);
    }

    /**
     * Get elements value
     * @param $identifier
     * @param $default
     * @return mixed
     */
    public function get($identifier, $default = null)
    {
        $element = $this->getElement($identifier);
        if ($element) {
            return $element->getValue();
        }

        return $default;
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
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Set link to list of variations
     * @param JBCartVariantList $list
     * @return JBCartVariantList
     */
    public function setList(JBCartVariantList $list)
    {
        $this->list = $list;

        return $list;
    }

    /**
     * @param array          $elements
     * @param AppData|array $options
     */
    public function add($elements, $options = array())
    {
        foreach($elements as $key => $element) {
            $this->elements[$key] = $this->_setElement($element, $options->get($key));
        }
        unset($options);
    }

    /**
     * Set link to basic variant
     * @param JBCartVariant $basic
     */
    public function setBasic(JBCartVariant $basic)
    {
        $this->basic = $basic;
    }

    /**
     * @param $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
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
     * @return int Old variant id
     */
    public function setId($id)
    {
        $old_id   = $this->id;
        $this->id = (int)$id;

        return $old_id;
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
     * Check if item in stock
     * @param integer $quantity
     * @return bool
     */
    public function inStock($quantity)
    {
        if ($element = $this->getElement('_balance')) {
            if ($element->inStock($quantity)) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * @param string $key - element -identifier
     * @return JBCartElementPrice|null
     */
    public function getElement($key)
    {
        return isset($this->elements[$key]) ? $this->elements[$key] : null;
    }

    /**
     * Return array of elements
     * @return array array of JBCartElementPrice
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @return array
     */
    public function getSimpleElements()
    {
        return array_filter($this->getElements(),
            create_function('$element', 'return $element->isCore() == false;'));
    }

    /**
     * @return int
     */
    public function countSimple()
    {
        return count(array_filter($this->getElements(),
            create_function('$element', 'return $element->isCore() == false;')));
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
            $price = $this->getPrice();
            $total = $price->minus($this->get('_discount', $this->value), true);

            if ($this->list instanceof JBCartVariantList) {
                $total = $this->list->addModifiers($total, true);
            }

            $this->total = $total;

            return $this->total;
        }

        throw new Exception('Variant cant calculate his own total price. Please use JBCartVariantList');
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

        $price = $this->value;
        if ($element = $this->getElement('_value')) {
            $price = $element->getValue();
            if ($element->isModifier() && !$this->isBasic()) {
                $price = $this->basic->get('_value')->add($price);
            }
        }

        $this->price = $price;
        if ($this->list->isOverlay === false) {
            $this->price = $price->add($this->get('_margin', $this->value));

            if ($this->list instanceof JBCartVariantList) {
                $this->price = $this->list->addModifiers($this->price, false);
            }
        }

        return $this->price->getClone();
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
     * @param array $options
     * @return $this
     */
    public function setData(array $options)
    {
        if (isset($options['elements']))
        {
            $elements = new AppData($options['elements']);
            foreach ($this->elements as $key => $element)
            {
                $this->elements[$key] = $this->_setElement($element, $elements->get($key, array()));
            }
        }

        return $this;
    }

    /**
     * @param JBCartElementPrice $element
     * @param array|string       elements data
     * @return mixed
     */
    protected function _setElement($element, $data = array())
    {
        $element->setVariant($this->id);
        if ($this->list instanceof JBCartVariantList) {
            if (!$this->isBasic()) {
                $data['_basic'] = $this->list->first()->get($element->identifier);
            }
        }

        if(!empty($data)) {
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