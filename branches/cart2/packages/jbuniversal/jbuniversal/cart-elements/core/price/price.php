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
    public $prices;

    /**
     * @type string
     */
    protected $_namespace = JBCart::ELEMENT_TYPE_PRICE;

    /**
     * @var JBHtmlHelper
     */
    protected $_jbhtml;

    /**
     * @var ElementJBPricePlain || ElementJBPriceCalc
     */
    protected $_jbprice;

    /**
     * @type JBStorageHelper
     */
    protected $_storage;

    /**
     * @type JBCartVariant
     */
    protected $_variant;

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
        $value = $this->get('value', 0);
        if (!isset($value) || empty($value)) {
            return false;
        }

        return true;
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
            return $this->renderLayout($layout, array(
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
        $element = $this->_variant->getElement($identifier);

        return $element;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->_jbprice->getData("{$this->config->get('_variant')}.{$this->identifier}.{$key}");
        return $this->_data->get($key, $default);
    }

    /**
     * Gets data array
     * @return array
     */
    public function data()
    {
        return $this->_data;
    }
    /**
     * @param      $key
     * @param null $default
     * @return mixed
     */
    public function options($key, $default = null)
    {
        return $this->_variant->options->get($key, $default);
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
        if (isset($this->prices)) {
            return $this->prices;
        }
        $total = $this->_jbprice->getList()->getTotal();
        $price = $this->_jbprice->getList()->getPrice();

        return $this->prices = array(
            'total' => $total->data(true),
            'price' => $price->data(true),
            'save'  => $total->minus($price, true)->data(true)
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
        if(!is_array($data)) {
            $this->set($key, $data);
        }

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param JBCartVariant $variant
     */
    public function setVariant($variant)
    {
        $this->_variant = $variant;
    }

    /**
     * @return ElementJBPrice
     */
    public function getJBPrice()
    {
        return $this->_jbprice;
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
     * Return price helper
     * @return JBPriceHelper
     */
    public function getHelper()
    {
        return $this->app->jbprice;
    }

    /**
     * Get default currency
     * @return mixed
     */
    public function currency()
    {
        return $this->_jbprice->currency();
    }

    /**
     * Get unique class/id for element
     * @param bool $unique
     * @return string
     */
    public function htmlId($unique = false)
    {
        $id = 'jbcart-' . $this->options('layout') . '-' . $this->options('item_id') . '-' . $this->identifier;
        if ($unique) {
            return $this->getId($id);
        }

        return $id;
    }

    /**
     * @param string $prefix
     * @return string
     */
    public function getId($prefix = 'unique-')
    {
        return $prefix . '-' . mt_rand(1000, 9999);
    }

    /**
     * @param $text
     * @return array
     */
    public function parseLines($text)
    {
        $text  = JString::trim($text);
        $lines = explode("\n", $text);

        $result = array();
        if (!empty($lines)) {
            foreach ($lines as $line) {
                $line          = JString::trim($line);
                $result[$line] = $line;
            }
        }

        return $result;
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
     * @param array $params
     * @return array
     */
    public function interfaceParams($params = array())
    {
        return null;
    }

    /**
     * Returns data when variant changes
     * @param array $params
     * @return null
     */
    public function renderAjax($params = array())
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
        $options  = array();
        $selected = (int)$this->options('selected', 0);

        if (!$this->hasOptions() || $selected) {
            $options = $this->_jbprice->elementOptions($this->identifier);

        } else if (!$selected) {
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
            $options = $this->parseLines($options);

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
     * @param  string  $name
     * @param  boolean $array
     * @return string
     */
    public function getControlName($name, $array = false)
    {
        $index      = $this->config->get('_variant');
        $element_id = $this->options('element_id');

        return "elements[{$element_id}][variations][{$index}][{$this->identifier}][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getRenderName($name)
    {
        $item_id  = $this->options('item_id');
        $layout   = $this->options('layout');
        $template = $this->options('template');

        return "params[{$item_id}{$layout}{$template}][{$this->identifier}][{$name}]";
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
        $isCache = (int)$this->options('cache', 0);
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
        $isCache = (int)$this->options('cache', 0);
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
