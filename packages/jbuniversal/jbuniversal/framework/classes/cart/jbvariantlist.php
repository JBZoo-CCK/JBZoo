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
class JBCartVariantList
{
    /**
     * Default variant key
     * @var string
     */
    public $_default = ElementJBPrice::BASIC_VARIANT;

    /**
     * Variations on/off
     * @var bool
     */
    protected $_isAdvanced = true;

    /**
     * Array of options when user add to cart
     * @var array
     */
    protected $_options = array();

    /**
     * @var ElementJBPrice
     */
    protected $_jbprice = null;

    /**
     * List of variants Objects
     * @var array
     */
    private $_variants = array();

    const DEFAULT_VARIANT = 'default';

    /**
     * Class constructor.
     * @help Create JBCartVariantList object after JBPrice template is set.
     * @param array          $list
     * @param ElementJBPrice $jbPrice
     * @param array          $options
     */
    public function __construct($list, $jbPrice, $options = array())
    {
        $this->_jbprice = $jbPrice;
        $this->_default = $jbPrice->defaultVariantKey();
        $this->_options = $this->_jbprice->app->data->create($options);

        //Add basic variant if he isn't set in $list
        if (!isset($list[$jbPrice::BASIC_VARIANT])) {
            $list[$jbPrice::BASIC_VARIANT] = $jbPrice->get('variations.' . $jbPrice::BASIC_VARIANT, array());

        }
        ksort($list);

        //Create variant instance
        if (!empty($list)) {
            foreach ($list as $id => $elements) {
                $elements = array_merge($elements, $this->_jbprice->getSystemElementsParams());
                if ((!$this->has($id)) && ($instance = $this->_createInstance($id, $jbPrice, $elements))) {
                    $this->set($instance);
                }
            }
        }
    }

    /**
     * Get variant by id if exists
     * @param  integer|string $id
     * @return JBCartVariant|false
     */
    public function get($id = ElementJBPrice::BASIC_VARIANT)
    {
        if ($this->has($id)) {
            return $this->_variants[$id];
        }

        return false;
    }

    /**
     * Check if JBCartVariant exists.
     * @param  integer|string $id
     * @return bool
     */
    public function has($id)
    {
        if (isset($this->_variants[$id])) {
            return true;
        }

        return false;
    }

    /**
     * Get all variants
     * @return array
     */
    public function all()
    {
        return $this->_variants;
    }

    /**
     * Get first variant
     * @return JBCartVariant
     */
    public function shift()
    {
        reset($this->_variants);

        return current($this->_variants);
    }

    /**
     * Advance the internal array pointer of an array and return value
     * @return JBCartVariant
     */
    public function next()
    {
        return next($this->_variants);
    }

    /**
     * Get default variant
     * @return false|JBCartVariant
     */
    public function byDefault()
    {
        return $this->get($this->_default);
    }

    /**
     * Get price for variant
     * @param string|int $id
     * @return JBCartValue
     */
    public function getPrice($id = self::DEFAULT_VARIANT)
    {
        if ($id == self::DEFAULT_VARIANT) {
            $id = $this->_default;
        }
        $variant = $this->get($id);

        return $this->addModifiers($variant->getPrice());
    }

    /**
     * Get Total price for variant
     * @return JBCartValue
     */
    public function getTotal()
    {
        if ($this->_jbprice->isOverlay()) {
            $total = $this->_calcTotal();
        } else {
            $total = $this->_plainTotal();
        }

        $total = $this->addModifiers($total, true);

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
    public function addModifiers(JBCartValue $total, $visible = false)
    {
        $cart = JBCart::getInstance();
        $data = $cart->getItem($this->getSessionKey());

        $order    = $cart->newOrder();
        $elements = $order->getModifiersItemPrice($this->_jbprice, $data);

        if (!empty($elements)) {
            foreach ($elements as $id => $element) {
                if ($visible && (int)$element->config->get('visible', 1)) {
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
        $result = array(
            '_default' => $this->_default,
            '_priceId' => $this->_jbprice->identifier,
            '_itemId'  => $this->_jbprice->getItem()->id
        );

        $values = (array)$this->values;
        if (!empty($values)) {
            foreach ($values as $key => $value) {
                //TODO Need to check value, method - issetOption
                if ($element = $this->_jbprice->getElement($key)) {
                    $element->bindData($value);
                    $result[$key] = $key . $element->getValue();
                }
            }
        }

        ksort($result);

        return md5(serialize($result));
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
                    //TODO Need to check value, method - issetOption
                    $element->bindData($value);
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
        if ($this->_jbprice->isOverlay()) {
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

        return $this->_jbprice->app->data->create($data);
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

        if ($element->issetOption($value)) {
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

        foreach ($variant->getElements() as $key => $element) {
            if ($element->isCore() && $this->_jbprice->getElementRenderParams($key)) {
                $data = $element->renderAjax();
                //return data if not null
                if (!is_null($data)) {
                    $result[$key] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Magic method to get access to protected property @_options
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->_options->get($property, null);
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
        $variant = $this->byDefault();
        $jbPrice = $this->_jbprice;
        $item    = $jbPrice->getItem();

        $data = array(
            'key'        => $this->getSessionKey(),
            'item_id'    => $item->id,
            'item_name'  => $item->name,
            'element_id' => $jbPrice->identifier,
            'total'      => $this->getTotal()->data(true),
            'quantity'   => (float)$this->quantity,
            'template'   => $jbPrice->getTemplate(),
            'layout'     => $jbPrice->getLayout(),
            'values'     => $this->getValues(),
            'elements'   => $variant->getElementsCartData(),
            'params'     => $jbPrice->elementsInterfaceParams(),
            'modifiers'  => $this->getModifiersRates(),
            'variant'    => $this->_default,
            'variations' => array(
                $jbPrice::BASIC_VARIANT => $jbPrice->get('variations.' . $jbPrice::BASIC_VARIANT),
                $this->_default         => $jbPrice->get('variations.' . $this->_default)
            )
        );

        return $data;
    }

    /**
     * Get data from ElementJBPriceCalc to add in cart
     * @return array
     */
    protected function _calcCartData()
    {
        $variant = $this->byDefault();
        $jbPrice = $this->_jbprice;
        $item    = $jbPrice->getItem();

        $data = array(
            'key'        => $this->getSessionKey(),
            'item_id'    => $item->id,
            'item_name'  => $item->name,
            'element_id' => $jbPrice->identifier,
            'total'      => $this->getTotal()->data(true),
            'quantity'   => (float)$this->quantity,
            'template'   => $jbPrice->getTemplate(),
            'layout'     => $jbPrice->getLayout(),
            'values'     => $this->getValues(),
            'elements'   => $variant->getElementsCartData(),
            'params'     => $jbPrice->elementsInterfaceParams(),
            'modifiers'  => $this->getModifiersRates(),
            'variant'    => $this->_default,
            'variations' => $jbPrice->quickSearch(array_keys($this->all()))
        );

        return $data;
    }

    /**
     * Get the total price for the variant element - ElementJBPricePlain
     * @return JBCartValue
     */
    protected function _plainTotal()
    {
        return $this->getPrice();
    }

    /**
     * Get the total price for the variant element - ElementJBPriceCalc
     * @return JBCartValue
     */
    protected function _calcTotal()
    {
        $total = $this->shift()->getTotal();

        if (count($this->all())) {
            foreach ($this->all() as $key => $variant) {
                if (!$variant->isBasic()) {
                    $total->add($variant->getTotal());
                }
            }
        }

        return $total;
    }

    /**
     * Create JBCartVariant instance
     * @param integer        $id
     * @param ElementJBPrice $jbPrice
     * @param array          $elements - array of element id => data
     * @return JBCartVariant
     */
    protected function _createInstance($id, $jbPrice, $elements = array())
    {
        $basic = null;
        if ($id != ElementJBPrice::BASIC_VARIANT) {
            $basic = $this->get($id);
        }

        return new JBCartVariant($id, $jbPrice, $elements, $basic);
    }

    /**
     * Set instance
     * @param JBCartVariant $variant
     */
    private function set(JBCartVariant $variant)
    {
        if (!isset($this->_variants[$variant->id()])) {
            $this->_variants[$variant->id()] = $variant;
        }
    }
}