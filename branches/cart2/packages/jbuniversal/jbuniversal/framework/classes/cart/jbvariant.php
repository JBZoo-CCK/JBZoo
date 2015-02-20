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
     * @type bool
     */
    public $isOverlay = false;

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
     * @type AppData
     */
    protected $options;

    /**
     * @type JBCartVariant
     */
    protected $basic;

    /**
     * Array of objects elements
     * @type array
     */
    protected $elements = array();

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
    public function __construct(array $elements = array(), $options = array(), JBCartVariantList $list = null)
    {
        $this->value = JBCart::val();

        // set variant list if exists
        if ($list instanceof JBCartVariantList)
        {
            $this->setList($list);
        }

        //set options
        if(isset($options))
        {
            $this->setOptions($options);
        }

        // set variant id
        if(isset($options['id']))
        {
            $this->setId($options['id']);
        }

        // set link to basic variant
        if($options['basic'] instanceof JBCartVariant)
        {
            $this->setBasic($options['basic']);
        }

        // remove basic key
        $this->options->remove('basic');

        //Bind elements
        if ($elements)
        {
            $this->elements = new AppData($elements);
        }

        //set elements data
        if(isset($options['data']))
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
     * @param string $identifier - element -identifier
     * @return JBCartElementPrice|null
     */
    public function getElement($identifier)
    {
        return $this->elements->get($identifier, false);
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
     * Get Total price for variant
     * @return JBCartValue
     * @throws Exception
     */
    public function getTotal()
    {
        if (!is_null($this->total)) {
            return $this->total;
        }

        if (!$this->isOverlay) {
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

        $this->price = $price->add($this->get('_margin', $this->value));
        if ($this->list instanceof JBCartVariantList) {
            $this->price = $this->list->addModifiers($this->price, false);
        }

        return $this->price;
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
        if(array_key_exists('isOverlay', $options)) {
            $this->isOverlay = $options['isOverlay'];
        }

        $data = new AppData($options['data']);
        $this->options->remove('data')->remove('id');

        foreach($this->elements as $key => $element)
        {
            $this->_setElement($element, $data->get($key));
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
        $element->config->set('_variant', $this->getId());

        if (!$this->isBasic() && $this->basic instanceof JBCartVariant) {
            $data['_basic'] = $this->basic->get($id);
        }
        $data['_options'] = $this->options;

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