<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 *
 * @package
 * @since    2.2
 */
class JBCartVariant
{
    /**
     * @var bool
     */
    public $overlay;

    /**
     * Id of variant
     *
     * @var integer
     */
    protected $_id;

    /**
     * Array of objects elements
     *
     * @var array
     */
    protected $_elements = array();

    /**
     * @var ElementJBPricePlain || ElementJBPriceCalc
     */
    protected $_jbprice;

    /**
     * Empty object to set defaults
     *
     * @var JBCartValue
     */
    private $value;

    /**
     * Class constructor
     *
     * @param integer $id
     * @param ElementJBPricePlain || ElementJBPriceCalc $jbPrice
     * @param array   $data
     */
    public function __construct($id, $jbPrice, $data = array())
    {
        $this->_jbprice  = $jbPrice;
        $this->_elements = $jbPrice->app->data->create();
        $this->overlay   = $jbPrice->isOverlay();

        $this->_id   = $id;
        $this->value = JBCart::val();

        $elements = $this->_jbprice->_getElements(array_keys((array)$data));

        if ($elements) {
            foreach ($elements as $id => $element) {
                $this->_setElement($element, $data[$id]);
            }
        }
    }

    /**
     * Get elements value
     *
     * @param $identifier
     * @param $default
     *
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
     *
     * @return int
     */
    public function id()
    {
        return (int)$this->_id;
    }

    /**
     * Check if variant is basic
     *
     * @return bool
     */
    public function isBasic()
    {
        return $this->id() === 0;
    }

    /**
     * Check if item in stock
     *
     * @param integer $quantity
     *
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
     * @param $identifier
     *
     * @return JBCartElementPrice|null
     */
    public function getElement($identifier)
    {
        return $this->_elements->get($identifier, false);
    }

    /**
     * Return array of elements
     *
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Get Total price for variant
     *
     * @return JBCartValue
     */
    public function getTotal()
    {
        $value = $this->getPrice();

        $margin   = $this->get('_margin', $this->value);
        $discount = $this->get('_discount', $this->value);

        $value
            ->add($margin->abs())
            ->minus($discount->abs());

        return $value;
    }

    /**
     * Get price for variant
     *
     * @return JBCartValue
     */
    public function getPrice()
    {
        return $this->get('_value', $this->value);
    }

    /**
     * @return array
     */
    public function getElementsCartData()
    {
        $elements = $this->getElements();
        $data     = array();

        if (!empty($elements)) {
            foreach ($elements as $key => $element) {

                if ($element->isCore()) {
                    $value = $element->getValue();

                    if ($value instanceof JBCartValue) {
                        $value = $value->dump();
                    }
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * If someone is accessed as a string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id();
    }

    /**
     * @param JBCartElementPrice $element
     * @param array|string       elements data
     *
     * @return mixed
     */
    protected function _setElement($element, $data = array())
    {
        $key = $element->identifier;

        $config = $element->config;

        $config->set('_variant', $this->id());
        $element->setConfig($config);

        if (!empty($data)) {
            $element->bindData($data);
        }

        $this->_elements[$key] = $element;

        return $element;
    }

}