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
 * @since 2.2
 */
abstract class JBCartElementPrice extends JBCartElement
{
    /**
     * Key of price variant.
     * @type int
     */
    public $variant;

    /**
     * Price element UUID.
     * @type string
     */
    public $element_id;

    /**
     * Id of Item.
     * @type int
     */
    public $item_id;

    /**
     * Show only selected options/all.
     * @type bool
     */
    public $showAll;

    /**
     * Unique hash. Generated in price element.
     * Hash based on element render params, price elements params, item_id, price data etc.
     * @see ElementJBPrice::hash()
     * @type string
     */
    protected $hash;

    /**
     * ElementJBPrice template.
     * @type string
     */
    protected $template;

    /**
     * Item layout.
     * @type string
     */
    protected $layout;

    /**
     * If element refers to the basic variant.
     * @type bool
     */
    protected $basic;

    /**
     * Required element or not when item add to cart.
     * @type bool
     */
    protected $required;

    /**
     * Is the cache on.
     * @type bool
     */
    protected $cache;

    /**
     * Is overlay mode. Instance of ElementJBPriceCalc
     * @see ElementJBPriceCalc
     * @type bool
     */
    protected $isOverlay;

    /**
     * Array of prices.
     * @example array(
     *     'total' => '....',
     *     'price' => '....',
     *     'save'  => '....'
     * )
     * @type array
     */
    protected $prices;

    /**
     * @var ElementJBPrice
     */
    protected $_jbprice;

    /**
     * @var JBHtmlHelper
     */
    protected $_jbhtml;

    /**
     * @todo args[4] $params ?
     * Constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbhtml    = $app->jbhtml;
        $this->_namespace = JBCart::ELEMENT_TYPE_PRICE;
    }

    /**
     * Get an instance of the price element.
     * @return ElementJBPrice
     * @throws JBCartElementPriceException
     */
    public function getJBPrice()
    {
        if (!$this->_jbprice instanceof ElementJBPrice) {
            throw new JBCartElementPriceException('Item price is not set in ' . get_class($this));
        }

        return $this->_jbprice;
    }

    /**
     * Set related price object.
     * @param ElementJBPrice $object
     */
    public function setJBPrice($object)
    {
        $this->_jbprice = $object;
    }

    /**
     * Set the variant key to which element belongs.
     * @param int $key
     */
    public function setVariant($key)
    {
        $this->variant = (int)$key;
    }

    /**
     * Unique string for related price elements and his elements.
     * @param string $hash
     */
    public function setHash($hash)
    {
        if ($this->hash === null) {
            $this->hash = $hash;
        }
    }

    /**
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     *
     * Is element belong to basic variant.
     * Basic variant means that the variants key is 0.
     * @return bool
     */
    public function isBasic()
    {
        return (bool)($this->variant === 0);
    }

    /**
     * @todo Complete method
     * Check if element is required.
     * @return int
     */
    public function isRequired()
    {
        if($this->required === null) {
            $this->required = (bool)((int)$this->config->get('required', 0) === 0);
        }

        return $this->required;
    }

    /**
     * Check element id
     * @param  string $identifier
     * @return bool
     */
    public function is($identifier)
    {
        return $this->identifier === $identifier;
    }

    /**
     * Get an element from the price.
     * Will be created new instance of JBCartElementPrice.
     * @param string $identifier Id of element
     * @return JBCartElement|mixed
     */
    public function getElement($identifier)
    {
        return $this->getJBPrice()->getElement($identifier);
    }

    /**
     * Return price helper.
     * @return JBPriceHelper
     */
    public function getHelper()
    {
        return $this->app->jbprice;
    }

    /**
     * Count elements data.
     * Other way to check if elements has no value.
     * @return int
     */
    public function count()
    {
        return count((array)$this->data());
    }

    /**
     * Set data through data array.
     * @param  array|string $data
     * @param  string       $key Default key for $data. If data is not array.
     * @return $this
     */
    public function bindData($data = array(), $key = 'value')
    {
        if (!is_array($data)) {
            $data = array($key => (string)$data);
        }

        return parent::bindData($data);
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
     * Check if element has value.
     * @param AppData|array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $value = $this->get('value', '');

        return ($value !== '' && $value !== null);
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
     * Returns data when variant changes.
     * @param AppData|array $params
     * @return mixed
     */
    public function renderAjax($params = array())
    {
        return null;
    }

    /**
     * Get params for widget
     * @param AppData|array $params
     * @return mixed
     */
    public function interfaceParams($params = array())
    {
        return array();
    }

    /**
     * Get elements value. If value not set trying to get it from basic variant.
     *
     * @param bool   $toString A string representation of the value.
     * @param string $key      Array key.
     * @param mixed  $default  Default value if data is empty.
     *
     * @return mixed|string|JBCartValue
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
     * Renders the edit form field.
     * Must be overloaded by the child class.
     * @return mixed
     */
    abstract public function edit();

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        // Important JBPrice JS
        $this->js('cart-elements:core/price/assets/js/price.js');

        // parent was realoded for JBPrice caching
        $group = $this->getElementGroup();
        $type  = $this->getElementType();

        $this->js('cart-elements:' . $group . '/' . $type . '/assets/js/' . $type . '.js');
        $this->css('cart-elements:' . $group . '/' . $type . '/assets/css/' . $type . '.css');
        $this->less('cart-elements:' . $group . '/' . $type . '/assets/less/' . $type . '.less');

        return $this;
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadEditAssets()
    {
        $group = $this->getElementGroup();
        $type  = $this->getElementType();

        $this->app->jbassets->js('cart-elements:' . $group . '/' . $type . '/assets/js/edit.js');

        return $this;
    }

    /**
     * @param  string  $name
     * @param  boolean $array
     * @return string
     */
    public function getControlName($name, $array = false)
    {
        return "elements[{$this->element_id}][variations][{$this->variant}][{$this->identifier}][{$name}]" . ($array ? '[]' : '');
    }

    /**
     * @param string $name
     * @param bool   $array
     * @return string
     */
    public function getRenderName($name, $array = false)
    {
        return "{$this->hash}[{$this->identifier}][{$name}]" . ($array ? '[]' : '');
    }

    /**
     * Get options for simple element
     * @param  bool $label - add option with no value
     * @return mixed
     */
    public function getOptions($label = true)
    {
        $options = $this->parseOptions(false);
        if (!$this->hasOptions())
        {
            $options = $this->getJBPrice()->elementOptions($this->identifier);
        }
        elseif (!$this->showAll)
        {
            $selected = $this->getJBPrice()->elementOptions($this->identifier);
            $options  = array_intersect_key($selected, $options);
        }

        if (false !== $label && count($options)) {
            $options[''] = $this->getLabel($label);

            ksort($options);
        }

        return $options;
    }

    /**
     * Parse options from config.
     * @param  bool $label - add option with no value
     * @return array
     */
    public function parseOptions($label = true)
    {
        $options = $this->config->get('options', '');
        $options = $this->parseLines($options);

        if ($label !== false && count($options))
        {
            $options[''] = $this->getLabel();
        }
        ksort($options);

        return $options;
    }

    /**
     * Check if element has options.
     * @return bool
     */
    public function hasOptions()
    {
        return $this->config->has('options');
    }

    /**
     * @todo Not completed
     * Check if element has option.
     * @param  string $value Option value
     * @return bool
     */
    public function hasOption($value)
    {
        $options = $this->parseOptions(false);

        return (in_array($value, $options, true) && count($options)) || array_key_exists($value, $options);
    }

    /**
     * @todo Not completed
     * Check if element has option.
     * @param  string $value Option value
     * @deprecated
     * @see JBCartElementPrice::hasOption()
     * @return bool
     */
    public function issetOption($value)
    {
        $options = $this->parseOptions(false);

        return (count($options)) && in_array($value, $options, true);
    }

    /**
     * Get unique string for element
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
     * Get parameter form object to render input form
     * @return Object
     */
    public function getConfigForm()
    {
        return parent::getConfigForm()->addElementPath(__DIR__);
    }

    /**
     * Get label for element template.
     * @param string $label
     * @return mixed
     */
    protected function getLabel($label = '')
    {
        $label = JString::trim($label);
        if ($label === 'ELEMENT_NAME' || $label === '') {
            $label = '- ' . $this->getName() . ' -';
        }

        return JText::_($label);
    }

    /**
     * @todo move to element _value
     * Array of prices
     * @return array
     */
    protected function getPrices()
    {
        if ($this->prices !== null) {
            return $this->prices;
        }
        $list = $this->getJBPrice()->getList();

        $total = $list->getTotal();
        $price = $list->getPrice();

        $this->prices = array(
            'total' => $total,
            'price' => $price,
            'save'  => $total->minus($price, true)
        );

        return $this->prices;
    }

    /**
     * Get default currency
     * @return mixed
     */
    protected function currency()
    {
        return $this->getJBPrice()->currency();
    }

    /**
     * @todo Use helper
     * @param string $value
     * @param array  $symbols
     * @return mixed
     */
    protected function clearSymbols($value, $symbols = array('%', '+', '-'))
    {
        return JString::str_ireplace($symbols, '', $value);
    }

    /**
     * @param string $prefix
     * @return string
     */
    protected function getId($prefix = 'unique-')
    {
        return $prefix . '-' . mt_rand(1000, 9999);
    }

    /**
     * @todo Use helper
     * @param $text
     * @return array
     */
    protected function parseLines($text)
    {
        $text   = JString::trim($text);
        $result = array();
        if ($text !== '' && $text !== null) {
            $lines = explode("\n", $text);
            foreach ($lines as $line) {
                $line          = JString::trim($line);
                $result[$line] = $line;
            }
        }

        return $result;
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
        if ($layout = $this->getLayout('_edit.php')) {
            $html = parent::renderLayout($layout, array(
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
     * @param  array $assets
     * @param string $method
     * @return $this
     */
    protected function addToStorage($assets, $method = 'js')
    {
        $assets = (array)$assets;
        $count  = count($assets);
        $this->app->jbassets->$method($assets);
        if ($this->cache && $count) {
            for ($i = 0; $i < $count; $i++) {
                $this->_addToStorage($assets[$i]);
            }
        }

        return $this;
    }

    /**
     * @todo Use helper
     * @param  string $file
     * @return $this|JBCartElementPrice
     */
    protected function less($file)
    {
        return $this->addToStorage($file, 'less');
    }

    /**
     * @todo Use helper
     * @param  string $file
     * @return $this|JBCartElementPrice
     */
    protected function js($file)
    {
        return $this->addToStorage($file, 'js');
    }

    /**
     * @todo Use helper
     * @param  string $file
     * @return $this|JBCartElementPrice
     */
    protected function css($file)
    {
        return $this->addToStorage($file, 'css');
    }

    /**
     * @param string $asset
     * @return $this
     */
    protected function _addToStorage($asset)
    {
        $this->getJBPrice()->toStorage($asset);

        return $this;
    }

    /**
     * Clone data
     */
    public function __clone()
    {
        $this->_data  = clone($this->_data);
        $this->config = clone($this->config);

        $this->prices  = null;
        $this->variant = null;
        $this->hash    = null;
    }

    /**
     * @param string $name  Property name
     * @param mixed  $value Property value
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            $this->{'set' . $name}($value);
        }
        $reflect = new ReflectionProperty(get_class(), $name);

        if($reflect->isPublic()) {
            $this->{$name} = $value;
        }
    }
}

/**
 * Class JBCartElementPriceException
 */
class JBCartElementPriceException extends JBCartElementException
{
}
