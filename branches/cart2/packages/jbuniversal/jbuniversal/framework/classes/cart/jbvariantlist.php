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
 * @package
 * @since    2.2
 */
class JBCartVariantList
{
    /**
     * Default variant key
     * @var string
     */
    protected $_default = ElementJBPrice::BASIC_VARIANT;

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

    /**
     * Class constructor.
     * @help Create JBCartVariantList objects after JBPrice template is set.
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
     * Get default variant
     * @return false|JBCartVariant
     */
    public function byDefault()
    {
        return $this->get($this->_default);
    }

    /**
     * Get base variant
     * @return JBCartVariant
     */
    public function shift()
    {
        return $this->_variants[ElementJBPrice::BASIC_VARIANT];
    }

    /**
     * Get Total price for variant
     * @return JBCartValue
     */
    public function getTotal()
    {
        if (!$this->_jbprice->isOverlay()) {

            return $this->_plainTotal();
        }

        return $this->_calcTotal();
    }

    /**
     * Get price for variant
     * @param string|integer $id
     * @return JBCartValue
     */
    public function getPrice($id = ElementJBPrice::BASIC_VARIANT)
    {
        $default = $this->get($id);
        $margin  = $default->get('_margin');

        return $default->get('_value', JBCart::val())->add($margin);
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
     * Get data from variant
     */
    public function getCartData()
    {
        if ($this->_jbprice->isOverlay()) {
            return $this->_jbprice->app->data->create($this->_calcCartData());
        }

        return $this->_jbprice->app->data->create($this->_plainCartData());
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
            '_itemId'  => $this->_jbprice->getItem()->id,
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
        $item    = $this->_jbprice->getItem();

        $data = array(
            'key'       => $this->getSessionKey(),
            'item_id'   => $item->id,
            'item_name' => $item->name,
            'total'     => $this->getTotal()->dump(),
            'quantity'  => (float)$this->quantity,
            'template'  => $this->_jbprice->getTemplate(),
            'layout'    => $this->_jbprice->getLayout(),
            'values'    => $this->getValues(),
            'elements'  => $variant->getElementsCartData(),
            'params'    => $this->_jbprice->elementsInterfaceParams()
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
        $item    = $this->_jbprice->getItem();

        $data = array(
            'key'       => $this->getSessionKey(),
            'item_id'   => $item->id,
            'item_name' => $item->name,
            'total'     => $this->getTotal()->dump(),
            'quantity'  => (float)$this->quantity,
            'template'  => $this->_jbprice->getTemplate(),
            'layout'    => $this->_jbprice->getLayout(),
            'values'    => $this->getValues(),
            'elements'  => $variant->getElementsCartData(),
            'params'    => $this->_jbprice->elementsInterfaceParams()
        );

        return $data;
    }

    /**
     * Get the total price for the variant element - ElementJBPricePlain
     * @return JBCartValue
     */
    protected function _plainTotal()
    {
        $default = $this->byDefault();
        $value   = JBCart::val();

        if ($element = $default->getElement('_value')) {
            $value = $element->getValue();
        }

        if (!$default->isBasic()) {
            if ($element && $element->isModifier()) {

                $basic = $this->getPrice(ElementJBPrice::BASIC_VARIANT);
                $value = $basic->add($value);
            }
        }

        $margin   = $default->get('_margin', JBCart::val());
        $discount = $default->get('_discount', JBCart::val());

        $value
            ->add($margin->abs())
            ->minus($discount->abs())->abs();

        return $value;
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
        return new JBCartVariant($id, $jbPrice, $elements);
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