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
 * Class JBCartElementPrice
 */
abstract class JBCartElementPrice extends JBCartElement
{
    public $total;

    public $price;

    /**
     * @type bool
     */
    public $isOverlay;

    /**
     * @type string
     */
    public $_namespace = JBCart::ELEMENT_TYPE_PRICE;

    /**
     * @var JBHtmlHelper
     */
    public $_jbhtml;

    /**
     * @var ElementJBPricePlain || ElementJBPriceCalc
     */
    public $_jbprice;

    /**
     * @type JBStorageHelper
     */
    public $_storage;

    /**
     * Constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbhtml  = $app->jbhtml;
        $this->_storage = $app->jbstorage;
    }

    /**
     * Check if element has value
     * @param AppData|array $params
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
     * @return JBCartElement|mixed|null
     */
    public function getElement($identifier)
    {
        $variant = $this->getList()->byDefault();
        $element = $variant->getElement($identifier);

        return $element;
    }

    /**
     * Get render parameters of any price element
     * @param $identifier
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
     * @param string $key
     * @param null   $default
     * @return mixed|null
     */
    public function getValue($key = 'value', $default = null)
    {
        $value = $this->get($key, $default);
        if ((JString::strlen($value) === 0) && ($this->isCore()) && (!$this->isBasic())) {
            $value = $this->get('_basic', $default);
        }

        return $value;
    }

    /**
     * Get prices
     * @return array
     */
    public function getPrices()
    {
        if(!isset($this->total)) {
            $this->total = $this->getList()->getTotal();
        }
        if(!isset($this->price)) {
            $this->price = $this->getList()->getPrice();
        }

        return array(
            'total' => $this->total,
            'price' => $this->price,
            'save'  => $this->total->minus($this->price, true)
        );
    }

    /**
     * Set data through data array.
     * @param  array  $data
     * @param  string $key
     * @return $this
     */
    public function bindData($data = array(), $key = 'value')
    {
        if (!is_array($data)) {
            $this->set($key, $data);

            return $this;
        }

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param ElementJBPrice $object
     * @throws JBCartElementPriceException
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
     * Return price helper
     * @return JBPriceHelper
     */
    public function getHelper()
    {
        return $this->app->jbprice;
    }

    /**
     * @return JBCartVariantList
     */
    public function getList()
    {
        return $this->_jbprice->getVariantList();
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
     * Get unique class/id for element
     * @param bool $unique
     * @return string
     */
    public function htmlId($unique = false)
    {
        $jbPrice = $this->_jbprice;

        $id = 'jbcart-' . $jbPrice->layout() . '-' . $jbPrice->getItem()->id . '-' . $this->identifier;
        if ($unique) {
            return $this->app->jbstring->getId($id);
        }

        return $id;
    }

    /**
     * @return JBCartVariant
     */
    public function isBasic()
    {
        $variant = (int)$this->config->get('_variant', ElementJBPrice::BASIC_VARIANT);

        return $variant === ElementJBPrice::BASIC_VARIANT;
    }

    /**
     * Check if element is required
     * @return int
     */
    public function isRequired()
    {
        return (int)$this->config->get('required', 0);
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
     * @param string $__layout layouts template file
     * @param array  $__args   layouts template file args
     * @return string
     */
    protected function renderEditLayout($__layout, $__args = array())
    {
        $html = parent::renderLayout($__layout, $__args);
        if ($html && $this->isBasic()) {
            $system = $this->getLayout('basic.php');

            $html = parent::renderLayout($system, array(
                'html' => $html
            ));
        }

        return $html;
    }

    /**
     * Renders the element using template layout file
     * @param string $__layout layouts template file
     * @param array  $__args   layouts template file args
     * @return string
     */
    protected function renderLayout($__layout, $__args = array())
    {
        $html = parent::renderLayout($__layout, $__args);
        if ($html) {
            $system = $this->getLayout('_system.php');
            $html   = parent::renderLayout($system, array(
                'html' => $html
            ));
        }

        return $html;
    }

    /**
     * Get options for simple element
     * @param  bool $label - add option with no value
     * @return mixed
     */
    public function getOptions($label = true)
    {
        $options = array();
        $jbPrice = $this->_jbprice;

        if (!$this->hasOptions() || (int)$jbPrice->config->get('only_selected', 1)) {
            $options = $jbPrice->elementOptions($this->identifier);

        } else if (!(int)$jbPrice->config->get('only_selected', 1)) {
            $options = $this->parseOptions();
        }

        if (!empty($options) && $label) {
            $options = array('' => ' - ' . JText::_('JBZOO_CORE_PRICE_OPTIONS_DEFAULT') . ' - ') + $options;
        }

        return $options;
    }

    /**
     * Parse options from element config
     * @param  bool $label - add option with no value
     * @return array
     */
    public function parseOptions($label = true)
    {
        $options = $this->config->get('options', array());
        if (!empty($options)) {
            $options = $this->app->jbstring->parseLines($options);

            if ($label) {
                $options = array('' => ' - ' . JText::_('JBZOO_CORE_PRICE_OPTIONS_DEFAULT') . ' - ') + $options;
            }
        }

        return $options;
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
     * @param  string $value Option value
     * @return bool
     */
    public function issetOption($value)
    {
        $options = $this->parseOptions(false);

        if ((!empty($options)) && in_array($value, $options)) {
            return true;
        }

        return false;
    }

    /**
     * @param          $name
     * @param  integer $index
     * @param  boolean $array
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
     * @return string
     */
    public function getRenderName($name)
    {
        $jbPrice  = $this->getJBprice();
        $itemId   = $jbPrice->getItem()->id;
        $layout   = $jbPrice->layout();
        $template = $jbPrice->getTemplate();

        return "params[{$itemId}{$layout}{$template}][{$this->identifier}][{$name}]";
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        static $default;

        if (!isset($default)) {
            $this->app->jbassets->js('cart-elements:core/price/assets/js/price.js');
            $this->_addToStorage('cart-elements:core/price/assets/js/price.js');
            $default = true;
        }

        return parent::loadAssets();
    }

    /**
     * @param  array $files
     * @return $this
     */
    public function addToStorage($files = array())
    {
        $isCache = (int)$this->_data->find('_options.cache', 0);
        if ($isCache && !empty($files)) {
            if (is_string($files)) {
                $this->_addToStorage($files);
            } else {
                foreach ($files as $file) {
                    if (!empty($file)) {
                        $this->_addToStorage($file);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param $file
     * @return $this
     */
    protected function _addToStorage($file)
    {
        $isCache = (int)$this->_data->find('_options.cache', 0);
        if ($isCache) {
            $file = JString::trim($file);
            $key  = md5(strtolower(get_called_class()) . $file);

            $this->_storage->set('assets', $file, $key);
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
     * @param $name
     * @param $value
     * @return bool
     */
    public function setProperty($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;

            return true;
        }

        return false;
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
