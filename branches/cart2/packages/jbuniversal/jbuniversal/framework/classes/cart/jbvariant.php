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
class JBCartVariant
{
    /**
     * @type bool
     */
    public $overlay;

    /**
     * Id of variant
     * @type integer
     */
    protected $id;

    /**
     * Array of objects elements
     * @type array
     */
    protected $elements = array();

    /**
     * Link to the basic variant
     * @type JBCartVariant
     */
    protected $basic;

    /**
     * @type ElementJBPrice
     */
    protected $price;

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
     * @param integer           $id
     * @param JBCartVariantList $jbList
     * @param array             $data
     * @param JBCartVariant     $basic link to the basic variant
     */
    public function __construct($id, $jbList, $data = array(), $basic = null)
    {
        $jbPrice     = $jbList->getJBPrice();
        $this->list  = $jbList;
        $this->price = $jbPrice;

        $this->elements = $jbPrice->app->data->create();
        $this->overlay  = $jbPrice->isOverlay();

        $this->id    = $id;
        $this->value = JBCart::val();

        if ($elements = $jbPrice->_getElements(array_keys((array)$data))) {
            foreach ($elements as $id => $element) {
                $this->_setElement($element, $data[$id]);
            }
        }

        if (!$this->isBasic()) {
            $this->basic = $basic;
        }

        unset($basic);
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
     * Get JBCartVariant id
     * @return int
     */
    public function id()
    {
        return (int)$this->id;
    }

    /**
     * Check if variant is basic
     * @return bool
     */
    public function isBasic()
    {
        return $this->id() === 0;
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
     */
    public function getTotal()
    {
        $price = $this->getPrice();
        if ($this->price->isOverlay()) {
            return $this->getCalcTotal($price);
        }

        return $this->getPlainTotal($price);
    }

    /**
     * Get price for variant
     * @return JBCartValue
     */
    public function getPrice()
    {
        $price = $this->value;
        if ($element = $this->getElement('_value')) {
            $price = $element->getValue();
            if ($element->isModifier() && !$this->isBasic()) {
                $price = $this->basic->get('_value')->add($price);
            }
        }

        if ($this->price->isOverlay()) {
            return $this->getCalcPrice($price);
        }

        return $this->getPlainPrice($price);
    }

    /**
     * @param  JBCartValue $price
     * @return JBCartValue
     */
    protected function getPlainTotal(JBCartValue $price)
    {
        $total = $price->minus($this->get('_discount', $this->value));

        return $this->list->addModifiers($total, true);
    }

    /**
     * @param JBCartValue $price
     * @return JBCartValue
     */
    protected function getPlainPrice(JBCartValue $price)
    {
        $price->add($this->get('_margin', $this->value));

        return $this->list->addModifiers($price, false);
    }

    /**
     * @param  JBCartValue $price
     * @return JBCartValue
     */
    protected function getCalcTotal(JBCartValue $price)
    {
        return $this->list->addModifiers($price, true);
    }

    /**
     * @param  JBCartValue $price
     * @return JBCartValue
     */
    protected function getCalcPrice(JBCartValue $price)
    {
        $all = $this->list->all();
        if (count($all)) {
            foreach ($all as $key => $variant) {
                if (!$variant->isBasic()) {
                    $price->add($variant->get('_value', $this->value));
                }
            }
        }

        return $this->list->addModifiers($price, false);
    }

    /**
     * If someone is accessed as a string
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id();
    }

    /**
     * @param JBCartElementPrice $element
     * @param array|string       elements data
     * @return mixed
     */
    protected function _setElement($element, $data = array())
    {
        $key    = $element->identifier;
        $config = $element->config;

        $config->set('_variant', $this->id());
        $element->setConfig($config);

        if (!empty($data)) {
            $element->bindData($data);
        }

        $this->elements[$key] = $element;

        return $element;
    }
}