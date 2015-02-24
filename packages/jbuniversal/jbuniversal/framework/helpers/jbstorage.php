<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alenxader Oganov <t_tapak@yahoo.com>
 */

/**
 * Class JBPriceHelper
 */
class JBStorageHelper extends AppHelper
{
    /**
     * @type ArrayObject
     */
    protected $paths = array();

    /**
     * @type ArrayObject
     */
    protected $configs = array();

    /**
     * @type ArrayObject
     */
    protected $elements = array();

    /**
     * @type ArrayObject
     */
    protected $assets = array();

    /** Class constructor
     *
     * @param App $app A reference to an App Object
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $elements       = JPATH_ROOT . '/media/zoo/applications/jbuniversal/cart-elements/';
        $this->assets   = new AppData();
        $this->elements = new AppData();
        $this->paths    = new AppData(array(
            'jbcartelementpricebalance'     => $elements . 'price/balance/balance.php',
            'jbcartelementpricebool'        => $elements . 'price/bool/bool.php',
            'jbcartelementpricebuttons'     => $elements . 'price/buttons/buttons.php',
            'jbcartelementpricecolor'       => $elements . 'price/color/color.php',
            'jbcartelementpricecurrency'    => $elements . 'price/currency/currency.php',
            'jbcartelementpricedate'        => $elements . 'price/date/date.php',
            'jbcartelementpricedescription' => $elements . 'price/description/description.php',
            'jbcartelementpricediscount'    => $elements . 'price/discount/discount.php',
            'jbcartelementpriceimage'       => $elements . 'price/image/image.php',
            'jbcartelementpricemargin'      => $elements . 'price/margin/margin.php',
            'jbcartelementpriceproperties'  => $elements . 'price/properties/properties.php',
            'jbcartelementpricequantity'    => $elements . 'price/quantity/quantity.php',
            'jbcartelementpriceradio'       => $elements . 'price/radio/radio.php',
            'jbcartelementpriceselect'      => $elements . 'price/select/select.php',
            'jbcartelementpricesku'         => $elements . 'price/sku/sku.php',
            'jbcartelementpricetext'        => $elements . 'price/text/text.php',
            'jbcartelementpricevalue'       => $elements . 'price/value/value.php',
            'jbcartelementpriceweight'      => $elements . 'price/weight/weight.php'
        ));

        if (!class_exists('JBCartElement', false)) {
            require($elements . 'core/element/element.php');
        }

        if (!class_exists('JBCartElementPrice', false)) {
            require($elements . 'core/price/price.php');
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getElement($id)
    {
        if ($this->elements->has($id)) {
            return $this->elements->get($id);
        }

        return false;
    }

    /**
     * @param $id
     * @param $element
     * @return $this
     */
    public function setElement($id, $element)
    {
        if (!$this->elements->has($id)) {
            $this->elements->set($id, $element);
        }

        return $element;
    }

    /**
     * @param       $route
     * @param       $key
     * @param mixed $default
     * @return null
     */
    public function get($route, $key = null, $default = false)
    {
        if (!$key) {
            return $this->$route;
        }

        return $this->$route->find($key, $default);
    }

    /**
     * @param      $route
     * @param      $value
     * @param      $key
     * @return $this
     */
    public function set($route, $value, $key)
    {
        if (!$this->$route->has($key)) {
            $this->$route->set($key, $value);
        }
    }

    /**
     * Check if exists property
     * @param $route
     * @param $key
     * @return bool
     */
    public function has($route, $key = null)
    {
        if (isset($this->$route) && $key === null) {
            return true;
        }

        return $this->$route->has($key);
    }

    /**
     * @param string $route
     * @param array  $args
     * @return mixed
     * @throws Exception
     */
    public function create($route = 'elements', $args = array())
    {
        if (method_exists($this, 'create' . $route)) {
            return call_user_func_array(array($this, 'create' . $route), $args);
        }

        $class = strtolower($args['class']);
        $path  = $this->get('paths', $class);

        if (!$class || !file_exists($path))
        {
            return false;
        }
        if (!class_exists($class, false))
        {
            require($path);
        }

        if (count($args) > 0)
        {
            $reflection = new ReflectionClass($class);
            $object     = $reflection->newInstanceArgs($args);
        }
        else
        {
            $object = new $class();
        }

        $this->configure($object, $args);
        $this->set($route, $object, $args['identifier']);

        return $object;
    }

    /**
     * @param mixed  $object
     * @param  array $properties
     * @return mixed
     */
    public function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    /**
     * @param array             $elements
     * @param array             $options
     * @return JBCartVariant
     */
    protected function createVariant($elements, $options = array())
    {
        $variant = new JBCartVariant($elements, $options);

        return $variant;
    }
}