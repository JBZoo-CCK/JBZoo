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
     * @type AppData
     */
    public $options;

    /**
     * @type JBCartValue
     */
    protected $total = null;

    /**
     * @type JBCartValue
     */
    protected $price = null;

    /**
     * Id of variant
     * @type integer
     */
    protected $id;

    /**
     * Array of objects elements
     * @type array
     */
    protected $_elements = array();

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
    public function __construct(array $elements = array(), array $options = array(), JBCartVariantList $list = null)
    {
        $this->value    = JBCart::val();

        // set variant list if exists
        if ($list instanceof JBCartVariantList)
        {
            $this->setList($list);
            unset($list);
        }

        //set elements data
        if(isset($options))
        {
            $this->setData($options);
        }

        //save options
        if(isset($options)) {
            $this->setOptions($options);

            unset($options);
        }

        //Bind elements
        if ($elements)
        {
            $this->add($elements);

            unset($elements);
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
        return isset($this->_elements[$key]) || array_key_exists($key, $this->_elements);
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
     * @param array $elements
     */
    public function add(array $elements)
    {
        foreach($elements as $key => $element) {
            $this->_elements[$key] = $this->_setElement($element);
        }
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
        $this->options = new AppData($options);
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
        return new ArrayIterator($this->_elements);
    }

    /**
     * Check if variant is basic
     * @return bool
     */
    public function isBasic()
    {
        return $this->getId() === 0;
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
        return isset($this->_elements[$key]) ? $this->_elements[$key] : null;
    }

    /**
     * Return array of elements
     * @return array array of JBCartElementPrice
     */
    public function getElements()
    {
        return $this->_elements;
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

        if (!$this->options->get('isOverlay')) {
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
        if ($this->options->get('isOverlay') === false) {
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
     * @return array
     */
    public function bindData()
    {
        /**
         * @type  string             $key
         * @type  JBCartElementPrice $element
         */
        $data = array();
        foreach ($this->_elements as $key => $element) {
            if ($this->elements->has($key)) {
                $element->bindData($this->elements->get($key));
                $data[$key] = (array)$element->data();
            }
        }

        return $data;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setData(array $options)
    {
        // set variant id
        if(isset($options['id']))
        {
            $this->setId($options['id']);
        }

        return $this;
    }

    /**
     * @param JBCartElementPrice $element
     * @param array|string       elements data
     * @return mixed
     */
    protected function _setElement(&$element, $data = array())
    {
        $id = $element->identifier;
        $element->config->set('_variant', $this->id);

        if (!$this->isBasic() && $this->list instanceof JBCartVariantList) {
            $data['_basic'] = $this->list->first()->get($id);
        }
        $element->setVariant($this);
        $element->bindData($data);

        return $element;
    }
}

/**
 * Class JBCartVariantException
 */
class JBCartVariantException extends AppException
{

}