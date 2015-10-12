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
     * @type AppData
     */
    protected $paths = array();

    /**
     * @type AppData
     */
    protected $configs = array();

    /**
     * @type AppData
     */
    protected $parameters = array();

    /**
     * @type AppData
     */
    protected $filter = array();

    /**
     * @type AppData
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

        $path     = JPATH_ROOT . '/media/zoo/applications/jbuniversal/cart-elements/';
        $elements = JFolder::folders($path . 'price/');

        $this->configs    = new AppData();
        $this->assets     = new AppData();
        $this->elements   = new AppData();
        $this->parameters = new AppData();
        $this->filter     = new AppData();
        $this->paths      = new AppData();

        foreach($elements as $element) {
            $this->paths->set('jbcartelementprice' . $element, $path . 'price/' . $element . '/' . $element . '.php');
        }
        
        if (!class_exists('JBCartElement')) {
            require($path . 'core/element/element.php');
        }

        if (!class_exists('JBCartElementPrice')) {
            require($path . 'core/price/price.php');
        }
    }

    /**
     * @param string $route
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get($route, $key = null, $default = false)
    {
        if (method_exists($this, 'get' . $route)) {
            $args = array_slice(func_get_args(), 1);

            return call_user_func_array(array($this, 'get' . $route), $args);
        }

        if (!$key)
        {
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
        if (!$this->$route->has($key))
        {
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
        if (isset($this->$route) && $key === null)
        {
            return true;
        }

        return $this->$route->has($key);
    }

    /**
     * @param $route
     * @param $value
     * @param $key
     * @return bool
     */
    public function add($route, $value, $key)
    {
        if ($key === null)
        {
            return false;
        }

        if (!$this->$route->has($key)) {
            $this->$route->set($key, new AppData(array()));
        }
        $this->$route->$key->append($value);

        return true;
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
            return call_user_func_array(array($this, 'create' . $route), array('args' => $args));
        }

        return $this->createObject($args['class'], $args);
    }

    /**
     * @param       $class
     * @param array $args
     * @param array $properties
     * @return object
     */
    protected function createObject($class, $args = array(), $properties = array())
    {
        if (count($args) > 0)
        {
            $reflection = new ReflectionClass($class);
            $object     = $reflection->newInstanceArgs($args);
        }
        else
        {
            $object = new $class();
        }
        if (count($properties)) {
            $this->configure($object, $properties);
        }

        return $object;
    }

    /**
     * @param  mixed  $object
     * @param  array $properties
     * @return mixed
     */
    public function configure(&$object, $properties)
    {
        $reflection = new ReflectionClass($object);
        foreach ($properties as $name => $value) {
            $setter = 'set' . $name;
            if ($reflection->hasMethod($setter)) {
                $object->$setter($value);

            } elseif ($reflection->hasProperty($name) && $reflection->getProperty($name)->isPublic()) {
                $object->$name = $value;

            }
        }

        return $object;
    }

    /**
     * @param string     $id
     * @param mixed $default
     * @return mixed
     */
    public function getElement($id, $default = null)
    {
        return $this->elements->get($id, $default);
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
     * @param $id
     * @return bool
     */
    public function hasElement($id)
    {
        return $this->elements->has($id);
    }

    /**
     * @param      $key
     * @param null $default
     * @return mixed
     */
    public function getConfigs($key, $default = null)
    {
        $key  = strtolower(trim($key));
        if(!isset($this->configs[$key]))
        {
            $this->configs->set($key, JBModelConfig::model()->getGroup('cart.' . $key)->get(JBCart::DEFAULT_POSITION));
        }

        return $this->configs->get($key, $default);
    }

    /**
     * @param      $key
     * @param      $id
     * @param null $default
     * @return mixed
     */
    public function getConfig($key, $id, $default = null)
    {
        $key  = strtolower(trim($key));
        $data = $this->getConfigs($key, array());

        return isset($data[$id]) ? $data[$id] : $default;
    }

    /**
     * @param string $key
     * @param array  $default
     * @return mixed
     */
    public function getParameters($key, $default = null)
    {
        $key = strtolower(trim($key));
        if (!isset($this->parameters[$key]))
        {
            $parameters = array();

            $storage    = (array)JBModelConfig::model()->getGroup('cart.' . $key, array());
            $parts      = explode('.', $key);
            foreach ($storage as $position => $elements) {
                $_index = 0;
                foreach ($elements as $index => $params) {
                    // Backward Compatibility. Delete later.
                    if(!is_numeric($index))
                    {
                        $index = $_index++;
                    }
                    $params['_position'] = $position;
                    $params['_index']    = $index;
                    $params['_template'] = end($parts);

                    $parameters[] = $params;
                }
            }
            $this->parameters->set($key, $parameters);
        }

        return $this->parameters->get($key, $default);
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getFilters($key, $default = null)
    {
        $key = strtolower(trim($key));
        if (!isset($this->filter[$key]))
        {
            $parameters = array();

            $storage    = (array)JBModelConfig::model()->getGroup('cart.' . $key, array());
            $parts      = explode('.', $key);
            foreach ($storage as $position => $elements) {
                $_index = 0;
                foreach ($elements as $index => $params) {
                    // Backward Compatibility. Delete later.
                    if(!is_numeric($index))
                    {
                        $index = $_index++;
                    }
                    $params['_position'] = $position;
                    $params['_index']    = $index;
                    $params['_template'] = end($parts);

                    $parameters[] = $params;
                }
            }
            $this->filter->set($key, $parameters);
        }

        return $this->filter->get($key, $default);
    }

    /**
     * @param string $key
     * @param string $id
     * @param mixed|null   $default
     * @return mixed
     */
    public function getParameter($key, $id, $default = null)
    {
        $key  = strtolower(trim($key));
        $data = $this->getParameters($key . '.' . $id, array());

        return !empty($data) ? $data : $default;
    }

    /**
     * @param array $args
     * @return JBCartElementPrice
     */
    protected function createElement($args = array())
    {
        $class = strtolower($args['class']);
        $path  = $this->get('paths', $class);
        if (!$class || !file_exists($path))
        {
            return false;
        }
        if (!class_exists($class))
        {
            require($path);
        }

        $object = $this->createObject($class, array(
            'app'   => $args['app'],
            'type'  => $args['type'],
            'group' => $args['group']
        ), array(
            'identifier' => $args['identifier'],
            'config'     => $args['config']
        ));
        $this->set('elements', $object, $args['identifier']);

        return $object;
    }

    /**
     * @param array $args key elements - Array of JBCartPriceElement objects
     *                    key options  - options for set properties
     * @return JBCartVariant
     */
    protected function createVariant($args = array())
    {
        $variant = $this->createObject('JBCartVariant', array(
            'elements' => $args['elements'],
            'options'  => $args['options']
        ));

        return $variant;
    }
}