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
    /**
     * Key of price variant
     * @type int
     */
    public $variant = 0;

    /**
     * Unique hash. Generated in price element.
     * Hash based on element render params, price elements params, item_id, price data etc.
     * @see ElementJBPrice
     * @type string
     */
    public $hash;

    /**
     * Id of item
     * @type int
     */
    public $item_id;

    /**
     * UUID of ElementJBPrice
     * @type string
     */
    public $element_id;

    /**
     * Is the cache
     * @type bool
     */
    public $isCache;

    /**
     * Is overlay mode
     * @type bool
     */
    public $isOverlay;

    /**
     * Show only selected options
     * @type bool
     */
    public $showAll;

    /**
     * ElementJBPrice template
     * @type string
     */
    public $template;

    /**
     * Item layout
     * @type string
     */
    public $layout;

    /**
     * @type array
     */
    public $prices;

    /**
     * @var ElementJBPrice
     */
    protected $_jbprice;

    /**
     * @var JBHtmlHelper
     */
    protected $_jbhtml;

    /**
     * @type string
     */
    protected $_namespace = JBCart::ELEMENT_TYPE_PRICE;

    /**
     * Constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbhtml = $app->jbhtml;
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
        $element = $this->_jbprice->getElement($identifier);

        return $element;
    }

    /**
     * Get element data
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
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
     * @return int
     */
    public function count()
    {
        return count((array)$this->_data);
    }

    /**
     * Get elements value
     * @param string $key      Array key.
     * @param mixed  $default  Default value if data is empty.
     * @param bool   $toString A string representation of the value.
     * @return mixed|string
     */
    public function getValue($toString = false, $key = 'value', $default = null)
    {
        $value = $this->get($key);
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

        $this->prices = array(
            'total' => $total->data(true),
            'price' => $price->data(true),
            'save'  => $total->minus($price, true)->data(true)
        );

        return $this->prices;
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
        }

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
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
     * @param int $key
     */
    public function setVariant($key)
    {
        $this->variant = $key;
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
        $id = 'jbcart-' . $this->layout . '-' . $this->item_id . '-' . $this->identifier;
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
        return $this->variant === ElementJBPrice::BASIC_VARIANT;
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
     * @param AppData|array $params
     * @return array
     */
    public function interfaceParams($params = array())
    {
        return array();
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
        $options = $this->parseOptions(false);
        if (!$this->hasOptions() || !$this->showAll) {
            $selected = $this->_jbprice->elementOptions($this->identifier);
            $options  = array_intersect_key($selected, $options);
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
        return "elements[{$this->element_id}][variations][{$this->variant}][{$this->identifier}][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * @param  string $name
     * @param bool    $array
     * @return string
     */
    public function getRenderName($name, $array = false)
    {
        return "{$this->hash}[{$this->identifier}][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->js('cart-elements:core/price/assets/js/price.js');
        parent::loadAssets();

        return $this;
    }

    /**
     * @param  string $file
     * @return $this|JBCartElementPrice
     */
    public function less($file)
    {
        return $this->addToStorage($file, 'less');
    }

    /**
     * @param  string $file
     * @return $this|JBCartElementPrice
     */
    public function js($file)
    {
        return $this->addToStorage($file, 'js');
    }

    /**
     * @param  string $file
     * @return $this|JBCartElementPrice
     */
    public function css($file)
    {
        return $this->addToStorage($file, 'css');
    }

    /**
     * @param  array $assets
     * @param string $method
     * @return $this
     */
    public function addToStorage($assets, $method = 'js')
    {
        /** @type JBAssetsHelper $assets */
        $assets = (array)$assets;
        $count  = count($assets);
        $this->app->jbassets->$method($assets);
        if ($this->isCache && $count) {
            for ($i = 0; $i < $count; $i++) {
                $this->_addToStorage($assets[$i]);
            }
        }

        return $this;
    }

    /**
     * @param string $value
     * @param array $symbols
     * @return mixed
     */
    public function clearSymbols($value, $symbols = array('%', '+', '-'))
    {
        $symbols = array_map(array('JString', 'trim'), (array)$symbols);

        return JString::str_ireplace($symbols, '', $value);
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
     * @param $asset
     * @return $this
     */
    protected function _addToStorage($asset)
    {
        $this->_jbprice->toStorage($asset);

        return $this;
    }

    /**
     * Clone data
     */
    public function __clone()
    {
        $this->_data  = clone($this->_data);
        $this->config = clone($this->config);
        $this->prices = null;
    }
}


/**
 * Class JBCartElementPriceException
 */
class JBCartElementPriceException extends JBCartElementException
{
}
