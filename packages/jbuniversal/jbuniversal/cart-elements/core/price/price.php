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
 * Class JBCartElementPrice
 */
abstract class JBCartElementPrice extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_PRICE;

    /**
     * @var JBModelConfig
     */
    protected $_jbconfig;

    /**
     * @var JBHtmlHelper
     */
    protected $_jbhtml;

    /**
     * @var ElementJBPricePlain || ElementJBPriceCalc
     */
    protected $_jbprice;

    /**
     * Constructor
     *
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbhtml   = $app->jbhtml;
        $this->_jbconfig = JBModelConfig::model();
    }

    /**
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $value = $this->getValue();
        if (!empty($value)) {
            return true;
        }

        return false;
    }

    /**
     * Get elements search data
     * @return null
     */
    public function getSearchData()
    {
        return null;
    }

    /**
     * @param  array $params
     * @return bool
     */
    public function hasFilterValue($params = array())
    {
        return true;
    }

    /**
     * @return mixed
     */
    abstract public function edit();

    /**
     * @param  array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params' => $params,
            ));
        }

        return false;
    }

    /**
     * @param string $identifier
     * @return bool|JBCartElement|null
     */
    public function getElement($identifier)
    {
        $variant = $this->getList()->byDefault();
        $element = $variant->getElement($identifier);

        return $element;
    }

    /**
     * Get render parameters of any price element
     *
     * @param $identifier
     *
     * @return array|mixed
     */
    public function getRenderParams($identifier = null)
    {
        if (is_null($identifier)) {
            $identifier = $this->identifier;
        }

        return $this->getJBPrice()->getElementRenderParams($identifier);
    }

    /**
     * Get elements value
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getValue($key = 'value', $default = null)
    {
        $value = $this->get($key, $default);

        if ((JString::strlen($value) === 0) && ($this->isCore()) && (!$this->isBasic())) {
            $variant = $this->getList()->shift();
            if ($element = $variant->getElement($this->identifier)) {
                $value = $element->get($key, $default);
            }
        }

        return $value;
    }

    /**
     * @param ElementJBPricePlain || ElementJBPriceCalc $object
     */
    public function setJBPrice($object)
    {
        $this->_jbprice = $object;
    }

    /**
     * @return ElementJBPricePlain || ElementJBPriceCalc
     */
    public function getJBPrice()
    {
        return $this->_jbprice;
    }

    /**
     * @return JBCartVariantList
     */
    public function getList()
    {
        return $this->getJBPrice()->getVariantList();
    }

    /**
     * Get default currency
     * @return mixed
     */
    public function currency()
    {
        return $this->getList()->currency;
    }

    /**
     * @return JBCartVariant
     */
    public function isBasic()
    {
        $variant = $this->getList()->get($this->config->get('_variant'));

        return $variant->isBasic();
    }

    /**
     * Get params for widget
     * @return array
     */
    public function interfaceParams()
    {
        return null;
    }

    /**
     * Returns data when variant changes
     * @return null
     */
    public function renderAjax()
    {
        return null;
    }

    /**
     * Renders the element using template layout file
     *
     * @param string $__layout layouts template file
     * @param array  $__args layouts template file args
     *
     * @return string
     */
    protected function renderLayout($__layout, $__args = array())
    {
        $html = parent::renderLayout($__layout, $__args);

        if ($html) {
            $system = $this->getLayout('system.php');

            return parent::renderLayout($system, array(
                'html' => $html
            ));
        }

        return $html;
    }

    /**
     * Get options for simple element
     * @return mixed
     */
    public function getOptions()
    {
        $options = array();
        $jbPrice = $this->_jbprice;
        if (!$this->hasOptions() || (int)$jbPrice->config->get('only_selected', 1)) {
            $options = $jbPrice->elementOptions($this->identifier);

        } else if (!(int)$jbPrice->config->get('only_selected', 1)) {
            $options = $this->parseOptions();
        }

        if (!empty($options)) {
            $options = array('' => ' - ' . JText::_('JBZOO_CORE_PRICE_OPTIONS_DEFAULT') . ' - ') + $options;
        }

        return $options;
    }

    /**
     * Parse options from element config
     * @return array|null
     */
    public function parseOptions()
    {
        $options = $this->config->get('options', array());

        if (!empty($options)) {

            $options = $this->app->jbstring->parseLines($options);
            $options = array('' => ' - ' . JText::_('JBZOO_CORE_PRICE_OPTIONS_DEFAULT') . ' - ') + $options;

            return $options;
        }

        return null;
    }

    /**
     * Check if element has options in config
     * @return bool
     */
    public function hasOptions()
    {
        return $this->config->has('options');
    }

    /**
     * Check if option isset in element
     *
     * @param $value
     *
     * @return bool
     */
    public function issetOption($value)
    {
        $options = $this->parseOptions();

        if ((!empty($options)) && in_array($value, $options)) {
            return true;
        }

        return false;
    }

    /**
     * @param          $name
     * @param  integer $index
     * @param  boolean $array
     *
     * @return string
     */
    public function getControlName($name, $index = null, $array = false)
    {
        $priceId = $this->getJBPrice()->identifier;

        if (is_null($index)) {
            $index = $this->config->get('_variant');
        }

        return "elements[{$priceId}][variations][{$index}][{$this->identifier}][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * @param  string $name
     *
     * @return string
     */
    public function getRenderName($name)
    {
        $itemId = $this->getJBprice()->getItem()->id;

        return "params[{$itemId}][{$this->identifier}][{$name}]";
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        static $isAdded;
        if (!isset($isAdded)) {
            $this->app->jbassets->js('cart-elements:core/price/assets/js/default.js');
            $isAdded = true;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function renderOrderEdit()
    {
        return $this->getValue();
    }

    /**
     * Clone data
     */
    public function __clone()
    {
        $this->_data  = clone($this->_data);
        $this->config = clone($this->config);
    }

}


/**
 * Class JBCartElementPriceException
 */
class JBCartElementPriceException extends JBCartElementException
{
}
