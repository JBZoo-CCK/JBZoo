<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class Element
 */
abstract class JBCartElement
{
    /**
     * @var String
     */
    public $identifier;

    /**
     * @var App
     */
    public $app;

    /**
     * @var AppData
     */
    public $config;

    /**
     * @var String
     */
    protected $_group;

    /**
     * @var JBOrder
     */
    protected $_order;

    /**
     * @var String
     */
    protected $_type;

    /**
     * @var Element callbacks
     */
    protected $_callbacks = array();

    /**
     * @var JBCartElementHelper
     */
    protected $_jbcartelement = null;

    /**
     * Constructor
     * @param App $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        // set app
        $this->app = $app;

        $this->config = $this->app->data->create();

        $this->_group = strtolower(trim($group));
        $this->_type  = strtolower(trim($type));

        $this->_jbcartelement = $this->app->jbcartelement;
    }

    /**
     * Set new config data
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $this->app->data->create($config);
    }

    /**
     * Gets the elements type
     * @return string
     */
    public function getElementType()
    {
        return $this->_type;
    }

    /**
     * Gets the elements type
     * @return string
     */
    public function getElementGroup()
    {
        return $this->_group;
    }

    /**
     * Set data through data array.
     * @param array $data
     */
    public function bindData($data = array())
    {
        if (isset($this->_order)) {
            $this->_order->elements->set($this->identifier, $data);
        }
    }


    /**
     * Gets the elements data
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->_order->elements->find("{$this->identifier}.{$key}", $default);
    }

    /**
     * Sets the elements data.
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->_order->elements[$this->identifier][$key] = $value;
        return $this;
    }


    /**
     * Gets data array
     * @return array
     */
    public function data()
    {
        if (isset($this->_order)) {
            return $this->_order->elements->get($this->identifier);
        }

        return array();
    }

    /**
     * Get element layout path and use override if exists
     * @param null $layout
     * @return string
     */
    public function getLayout($layout = null)
    {
        // init vars
        $type = $this->getElementType();

        // set default
        if ($layout == null) {
            $layout = "{$type}.php";
        }

        // find layout
        return $this->app->path->path("elements:{$type}/tmpl/{$layout}");
    }

    /**
     * Get related order object
     * @return JBOrder
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Get related type object
     * @return String
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get element group
     * @return bool
     */
    public function getGroup()
    {
        return $this->getMetadata('group');
    }

    /**
     * Set related item object
     * @param JBOrder $order
     */
    public function setOrder(JBOrder $order)
    {
        $this->_order = $order;
    }

    /**
     * Set related type object
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $value = $this->get('value', $this->config->get('default'));
        return !empty($value);
    }

    /**
     * Renders the element
     * @param array $params
     * @return mixed|string
     */
    public function render($params = array())
    {
        // render layout
        if ($layout = $this->getLayout()) {
            return $this->renderLayout($layout, array('value' => $this->get('value')));
        }

        return $this->get('value');
    }

    /**
     * Renders the element using template layout file
     * @param $__layout layouts template file
     * @param array $__args layouts template file args
     * @return string
     */
    protected function renderLayout($__layout, $__args = array())
    {
        // init vars
        if (is_array($__args)) {
            foreach ($__args as $__var => $__value) {
                $$__var = $__value;
            }
        }

        // render layout
        $__html = '';
        ob_start();
        include($__layout);
        $__html = ob_get_contents();
        ob_end_clean();

        return $__html;
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        return $this;
    }

    /**
     * Load elements css/js config assets
     * @return $this
     */
    public function loadConfigAssets()
    {
        return $this;
    }

    /**
     * Register a callback function
     * @param $method
     */
    public function registerCallback($method)
    {
        if (!in_array(strtolower($method), $this->_callbacks)) {
            $this->_callbacks[] = strtolower($method);
        }
    }

    /**
     * Execute elements callback function
     * @param $method
     * @param array $args
     */
    public function callback($method, $args = array())
    {
        // try to call a elements class method
        if (in_array(strtolower($method), $this->_callbacks) && method_exists($this, $method)) {

            // call method
            $res = call_user_func_array(array($this, $method), $args);

            // output if method returns a string
            if (is_string($res)) {
                echo $res;
            }
        }
    }

    /**
     * Get parameter form object to render input form
     * @return AppParameterForm
     */
    public function getConfigForm()
    {
        // get form
        $form = $this->app->parameterform->create();

        $coreList       = $this->_jbcartelement->getAllCore();
        $coreElementXml = $this->app->path->path("cart-elements:core/element/element.xml");

        // get config xml files
        $params = array();
        $class  = new ReflectionClass($this);
        while ($class !== false) {

            preg_match('#^JBCartElement(' . implode('|', $coreList) . ')(.*)#i', $class->getName(), $matches);
            if (!$matches) {
                $matches = array('', '', ''); // default values
            }

            $group = strtolower(trim($matches[1]));
            $type  = strtolower(trim($matches[2]));
            $xml   = $coreElementXml;

            if ($group && $type) {
                $xml = $this->app->path->path("cart-elements:$group/$type/$type.xml");

            } else if ($group) {
                $xml = $this->app->path->path("cart-elements:core/$group/$group.xml");
            }

            array_unshift($params, $xml);
            $class = $class->getParentClass();
        }

        // trigger configparams event
        $event  = $this->app->event->create($this, 'cart-element:configparams')->setReturnValue($params);
        $params = $this->app->event->dispatcher->notify($event)->getReturnValue();

        // skip if there are no config files
        if (empty($params)) {
            return null;
        }

        // add config xml files
        foreach ($params as $xml) {
            $form->addXML($xml);
        }

        // set values
        $form->setValues($this->config);

        // add reference to element
        $form->element = $this;

        // trigger configform event
        $event = $this->app->event->create($this, 'cart-element:configform', compact('form'));
        $this->app->event->dispatcher->notify($event);

        return $form;
    }

    /**
     * Get elements xml meta data
     * @param null $key
     * @return bool
     */
    public function getMetaData($key = null)
    {
        $data = array();
        $type = $this->getElementType();
        $xml  = $this->loadXML();

        if (!$xml) {
            return false;
        }

        $data['type']         = $xml->attributes()->type ? (string)$xml->attributes()->type : 'Unknown';
        $data['group']        = $xml->attributes()->group ? (string)$xml->attributes()->group : 'Unknown';
        $data['hidden']       = $xml->attributes()->hidden ? (string)$xml->attributes()->hidden : 'false';
        $data['trusted']      = $xml->attributes()->trusted ? (string)$xml->attributes()->trusted : 'false';
        $data['orderable']    = $xml->attributes()->orderable ? (string)$xml->attributes()->orderable : 'false';
        $data['name']         = (string)$xml->name;
        $data['creationdate'] = $xml->creationDate ? (string)$xml->creationDate : 'Unknown';
        $data['author']       = $xml->author ? (string)$xml->author : 'Unknown';
        $data['copyright']    = (string)$xml->copyright;
        $data['authorEmail']  = (string)$xml->authorEmail;
        $data['authorUrl']    = (string)$xml->authorUrl;
        $data['version']      = (string)$xml->version;
        $data['description']  = (string)$xml->description;

        $data = $this->app->data->create($data);

        return $key == null ? $data : $data->get($key);
    }

    /**
     * Retrieve Element XML file info
     * @return SimpleXMLElement
     */
    public function loadXML()
    {
        $type  = $this->getElementType();
        $group = $this->getElementGroup();

        return simplexml_load_file($this->app->path->path("cart-elements:$group/$type/$type.xml"));
    }

    /**
     * Gets the controle name for given name
     * @param $name
     * @param bool $array
     * @return string
     */
    public function getControlName($name, $array = false)
    {
        return "elements[{$this->identifier}][{$name}]" . ($array ? "[]" : "");
    }

    /**
     * Check if element is accessible with users access rights
     * @param null $user
     * @return mixed
     */
    public function canAccess($user = null)
    {
        return $this->app->user->canAccess($user, $this->config->get('access', $this->app->joomla->getDefaultAccess()));
    }

    /**
     * Get path to element's base directory
     * @return mixed
     */
    public function getPath()
    {
        return $this->app->path->path("cart-elements:" . $this->getElementGroup() . '/' . $this->getElementType());
    }

}

/**
 * Class JBCartElementException
 */
class JBCartElementException extends AppException
{
}