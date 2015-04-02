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
 * Class JBCartVariantList
 * @since    2.2
 */
class JBCartVariantList extends ArrayObject
{
    /**
     * Default variant key
     * @var string
     */
    public $default = 0;

    /**
     * @type string
     */
    public $session_key;

    /**
     * Array of options when user add to cart
     * @var AppData|array
     */
    public $options = array();

    /**
     * @type JBStorageHelper
     */
    protected $_storage;

    /**
     * @type ElementJBPrice
     */
    protected $_jbprice;

    /**
     * List of variants Objects
     * @var array
     */
    private $variants = array();

    const DEFAULT_VARIANT = 'default';

    /**
     * Class constructor.
     * @help Create JBCartVariantList object after JBPrice template is set.
     * @param array $list
     * @param array $options
     */
    public function __construct($list, $options = array())
    {
        $this->setFlags(ArrayObject::STD_PROP_LIST);

        //sort ascending
        ksort($list);

        // set default variant key
        if (isset($options['default'])) {
            $this->setDefault($options['default']);
        }

        // set ElementJBPrice if exists
        if (isset($options['element'])) {
            $this->setJBPrice($options['element']);
            unset($options['element']);
        }
        // set options
        if (!empty($options) && isset($options)) {
            $this->setOptions($options);
            unset($options);
        }

        //add variations
        if (!empty($list)) {
            $this->add($list);
            unset($list);
        }
    }

    /**
     * Get variant by id if exists.
     * @param int $key
     * @return JBCartVariant
     */
    public function get($key = ElementJBPrice::BASIC_VARIANT)
    {
        return isset($this->variants[$key]) ? $this->variants[$key] : null;
    }

    /**
     * @param  integer       $key
     * @param  JBCartVariant $variant
     * @return bool
     * @throws Exception
     */
    public function set($key, $variant)
    {
        if (!$variant instanceof JBCartVariant) {
            throw new Exception('In Method: ' . __FUNCTION__ . ' values of array must be an instance of JBCartVariant.');
        }

        $this->variants[$key] = $variant;
    }

    /**
     * @param  array $list
     * @return $this
     * @throws Exception
     */
    public function add(array $list = array())
    {
        foreach ($list as $key => $variant) {
            $variant->setList($this)->bindData();
            $this->set($key, $variant);
        }

        return $this;
    }

    /**
     * Check if JBCartVariant exists.
     * @param  integer $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->variants[$key]) || array_key_exists($key, $this->variants);
    }

    /**
     * Get all variants
     * @return array
     */
    public function all()
    {
        return $this->variants;
    }

    /**
     * @return JBCartVariant
     */
    public function shift()
    {
        return $this->first();
    }

    /**
     * @return JBCartVariant
     */
    public function first()
    {
        return reset($this->variants);
    }

    /**
     * @return JBCartVariant
     */
    public function last()
    {
        return end($this->variants);
    }

    /**
     * @return int
     */
    public function key()
    {
        return key($this->variants);
    }

    /**
     * @return JBCartVariant
     */
    public function next()
    {
        return next($this->variants);
    }

    /**
     * @return JBCartVariant
     */
    public function current()
    {
        return current($this->variants);
    }

    /**
     * @deprecated
     * @return false|JBCartVariant
     */
    public function byDefault()
    {
        return $this->get($this->default);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->variants);
    }

    /**
     * @param $key
     */
    public function setDefault($key)
    {
        if ($this->default != $key) {
            $this->default = $key;
        }
    }

    /**
     * @param array $options
     * @return AppData|array
     */
    public function setOptions($options = array())
    {
        if (!empty($this->options)) {
            $options = array_merge((array)$this->options, $options);
        }

        if (!empty($options)) {
            $this->options = new AppData($options);
        }

        return $this->options;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->variants);
    }

    /**
     * @return ElementJBPrice
     */
    public function getJBPrice()
    {
        return $this->_jbprice;
    }

    /**
     * @param ElementJBPrice $element
     * @throws Exception
     */
    public function setJBPrice($element)
    {
        if (!$element instanceof ElementJBPrice) {
            throw new Exception('In Method: ' . get_class() . ' - ' . __FUNCTION__ . ' first argument must be instance of ElementJBPrice.');
        }

        $this->_jbprice = $element;
    }

    /**
     * Get price for variant
     * @return JBCartValue
     */
    public function getPrice()
    {
        if ($this->isOverlay) {
            return $this->_calcPrice();
        }

        return $this->_plainPrice();
    }

    /**
     * Get Total price for variant
     * @return JBCartValue
     */
    public function getTotal()
    {
        if ($this->isOverlay) {
            $total = $this->_calcTotal();
        } else {
            $total = $this->_plainTotal();
        }

        if ($total->isNegative()) {
            $total->setEmpty();
        }

        return $total;
    }

    /**
     * Add modifiers to the total or price
     * @param JBCartValue $total
     * @param bool        $visible
     * @return JBCartValue
     */
    public function addModifiers($total, $visible = false)
    {
        $cart = JBCart::getInstance();
        $data = $cart->getItem($this->getSessionKey());

        $order    = $cart->newOrder();
        $elements = $order->getModifiersItemPrice($this->_jbprice, $data);

        if (!empty($elements)) {
            foreach ($elements as $id => $element) {
                if ((int)$visible === (int)$element->config->get('visible', 1)) {
                    $element->modify($total);
                }
            }
        }

        return $total;
    }

    /**
     * Get modifiers rates
     * @return array
     */
    public function getModifiersRates()
    {
        $cart = JBCart::getInstance();
        $data = $cart->getItem($this->getSessionKey());

        $order    = $cart->newOrder();
        $elements = $order->getModifiersItemPrice($this->_jbprice, $data);

        $modifiers = array();
        if (!empty($elements)) {
            foreach ($elements as $id => $element) {
                $modifiers[$id] = (array)$element->getOrderData();
            }
        }

        return $modifiers;
    }

    /**
     * Get unique key by few parameters - item_id, element_id, variant_id, key and value of selected parameters.
     * @return string
     */
    public function getSessionKey()
    {
        if (!empty($this->session_key)) {
            return $this->session_key;
        }

        $result = array(
            '_default' => $this->options->get('default'),
            '_priceId' => $this->options->get('element_id'),
            '_itemId'  => $this->options->get('item_id')
        );

        $values = (array)$this->values;
        if (!empty($values)) {
            $result['values'] = $values;
        }
        ksort($result);

        $this->session_key = md5(serialize($result));

        return $this->session_key;
    }

    /**
     * Get readable values - element name -> value
     * @return array
     */
    public function getValues()
    {
        $result = array();
        $values = (array)$this->values;
        if (!empty($values)) {
            foreach ($values as $key => $value) {
                if ($element = $this->_jbprice->getElement($key)) {
                    $element->bindData($value);
                    //TODO Need to check value, method - issetOption
                    $result[$element->getName()] = $element->getValue();
                }
            }
        }

        return $result;
    }

    /**
     * Get data from variant
     */
    public function getCartData()
    {
        if ($this->isOverlay) {
            $data = $this->_calcCartData();
        } else {
            $data = $this->_plainCartData();
        }

        // TODO remove hack
        if (isset($data['params']['_currency'])) {
            unset($data['params']['_currency']);
        }

        if (isset($data['params']['_buttons'])) {
            unset($data['params']['_buttons']);
        }

        /** @type AppData */

        return new AppData($data);
    }

    /**
     * Check if option isset in element
     * @param $element
     * @param $value
     * @return bool|string
     */
    public function issetOption($element, $value)
    {
        $element->bindData($value);
        $value = $element->getValue();

        if ($element->hasOption($value)) {
            return $value;
        }

        return false;
    }

    /**
     * Render core variant element when he changes
     * @return array
     */
    public function renderVariant()
    {
        $variant = $this->byDefault();
        $result  = array();

        foreach ($variant->core() as $key => $element) {
            if ($params = $this->_jbprice->getElementRenderParams($key)) {
                $data = $element->renderAjax(new AppData($params));
                //return data if not null
                if ($data !== null) {
                    $result[$key] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Get elements data
     * @return array
     */
    public function defaultVariantCartData()
    {
        $data    = array();
        $variant = $this->byDefault();
        if ($variant->count('core')) {
            foreach ($variant->core() as $key => $element) {
                $value = $element->getValue(true);

                if ($key == '_properties') { //TODO HACK for multiplicity in properties element
                    $value = (array)$element->data();
                }
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Magic method to get access to protected property @_options
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->options->get($property, null);
    }

    /**
     * Magic method to call methods from default variant
     * @param string $method
     * @param array  $args
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args = array())
    {
        $default = $this->byDefault();
        if (method_exists($default, $method)) {
            return call_user_func(array($default, $method), $args);
        }

        throw new Exception ('Function "' . $method . '" in class "' . get_class($default) . '" doesn\'t exist');
    }

    /**
     * Get data from ElementJBPricePlain to add in cart
     * @return array
     */
    protected function _plainCartData()
    {
        $jbPrice = $this->_jbprice;
        $data    = array(
            'key'        => $this->getSessionKey(),
            'item_id'    => $this->item_id,
            'item_name'  => $this->item_name,
            'element_id' => $this->element_id,
            'total'      => $this->getTotal()->data(true),
            'quantity'   => (float)$this->quantity,
            'template'   => $this->template,
            'values'     => $this->getValues(),
            'elements'   => $this->defaultVariantCartData(),
            'params'     => $jbPrice->elementsInterfaceParams(),
            'modifiers'  => $this->getModifiersRates(),
            'variant'    => $this->default,
            'variations' => $jbPrice->defaultList(),
            'isOverlay'  => false
        );

        return $data;
    }

    /**
     * Get data from ElementJBPriceCalc to add in cart
     * @return array
     */
    protected function _calcCartData()
    {
        $jbPrice = $this->_jbprice;

        $data = array(
            'key'        => $this->getSessionKey(),
            'item_id'    => $this->item_id,
            'item_name'  => $this->item_name,
            'element_id' => $this->element_id,
            'total'      => $this->getTotal()->data(true),
            'quantity'   => (float)$this->quantity,
            'template'   => $this->template,
            'values'     => $this->getValues(),
            'elements'   => $this->defaultVariantCartData(),
            'params'     => $jbPrice->elementsInterfaceParams(),
            'modifiers'  => $this->getModifiersRates(),
            'variant'    => 0,
            'variations' => $jbPrice->quickSearch(array_keys($this->all())),
            'isOverlay'  => true
        );

        return $data;
    }

    /**
     * @return JBCartValue
     */
    protected function _plainPrice()
    {
        return $this->byDefault()->getPrice();
    }

    /**
     * @return JBCartValue
     */
    protected function _calcPrice()
    {
        $price = $this->first()->getPrice();
        if ($this->count() > 1) {
            foreach ($this->all() as $variant) {
                if (!$variant->isBasic()) {
                    $price->add($variant->getPrice());
                }
            }
        }

        return $this->addModifiers($price, false);
    }

    /**
     * Get the total price for the variant element - ElementJBPricePlain
     * @return JBCartValue
     */
    protected function _plainTotal()
    {
        return $this->byDefault()->getTotal();
    }

    /**
     * Get the total price for the variant element - ElementJBPriceCalc
     * @return JBCartValue
     */
    protected function _calcTotal()
    {
        $price = $this->_calcPrice();

        return $this->addModifiers($price, true);
    }

    /**
     *  Clear all variants
     */
    public function clear()
    {
        $this->variants = null;
        $this->options  = null;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function remove($id)
    {
        unset($this->variants[$id]);

        return $this;
    }
}