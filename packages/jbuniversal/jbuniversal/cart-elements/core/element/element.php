<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
    const DEFAULT_GROUP = '_default';

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
     * @var JBCartOrder
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
     * @var SimpleXMLElement
     */
    protected $_xmlData = null;

    /**
     * @var JSONData
     */
    protected $_metaData = null;

    /**
     * @var string
     */
    protected $_namespace = JBCart::ELEMENT_TYPE_DEFAULT;

    /**
     * @var JSONData
     */
    protected $_data = array();

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney = array();

    /**
     * Constructor
     * @param App    $app
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

        $this->_data = $this->app->data->create($this->_data);
    }

    /**
     * Load custom languages
     */
    protected function _loadLangs()
    {
        JFactory::getLanguage()->load('elem_' . $this->getElementType(), $this->getPath(), null, true);
    }

    /**
     * Set new config data
     * @param $config
     */
    public function setConfig($config)
    {
        if (is_array($config)) {
            $config = $this->app->data->create($config);
        }

        $this->config = $config;
    }

    /**
     * Gets the elements type
     * @return string
     */
    public function getElementType()
    {
        return strtolower($this->_type);
    }

    /**
     * Gets the elements type
     * @return string
     */
    public function getElementGroup()
    {
        return strtolower($this->_group);
    }

    /**
     * Set data through data array.
     * @param array $data
     * @return $this
     */
    public function bindData($data = array())
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Gets the elements data
     * @param      $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $result = $this->_data->get($key, $default);
        if ($result === null) {
            $result = $default;
        }

        return $result;
    }

    /**
     * Sets the elements data.
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->_data->set($key, $value);

        return $this;
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
     * Get element layout path and use override if exists
     * @param null|string $layout
     * @return string
     */
    public function getLayout($layout = null)
    {
        // init vars
        $type  = $this->getElementType();
        $group = $this->getElementGroup();

        // set default
        if (empty($layout)) {
            $layout = $type . '.php';
        } else if (strpos($layout, '.php') === false) {
            $layout .= '.php';
        }

        // own layout
        $layoutPath = $this->app->path->path("cart-elements:{$group}/{$type}/tmpl/{$layout}");

        // parent option
        if (empty($layoutPath)) {
            $layoutPath = $this->app->path->path("cart-elements:{$group}/option/tmpl/{$layout}");
        }

        // parent group
        if (empty($layoutPath)) {
            $layoutPath = $this->app->path->path("cart-elements:core/{$group}/tmpl/{$layout}");
        }

        // global
        if (empty($layoutPath)) {
            $layoutPath = $this->app->path->path("cart-elements:core/element/tmpl/{$layout}");
        }

        return $layoutPath;
    }

    /**
     * Get related order object
     * @return JBCartOrder
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
     * @param JBCartOrder $order
     */
    public function setOrder(JBCartOrder $order)
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
        $paramLayout = $params->get('layout') ? $params->get('layout') . '.php' : null;

        if ($layout = $this->getLayout($paramLayout)) {
            return $this->renderLayout($layout, array(
                'params' => $params,
                'value'  => $this->get('value'),
                'order'  => $this->getOrder(),
            ));
        }

        return null;
    }

    /**
     * @param  string $layout
     * @param  array  $vars
     * @return null|string
     */
    protected function _partial($layout, $vars = array())
    {
        $vars['order'] = $this->getOrder();

        if ($layout = $this->getLayout($layout . '.php')) {
            return self::renderLayout($layout, $vars);
        }

        return null;
    }


    /**
     * Renders the element using template layout file
     * @param string $__layout layouts template file
     * @param array  $__args   layouts template file args
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
        $group = $this->getElementGroup();
        $type  = $this->getElementType();

        $jbassets = $this->app->jbassets;
        $jbassets->js('cart-elements:' . $group . '/' . $type . '/assets/js/' . $type . '.js');
        $jbassets->css('cart-elements:' . $group . '/' . $type . '/assets/css/' . $type . '.css');
        $jbassets->less('cart-elements:' . $group . '/' . $type . '/assets/less/' . $type . '.less');

        return $this;
    }

    /**
     * Load elements css/js config assets
     * @return $this
     */
    public function loadConfigAssets()
    {
        $this->app->path->register($this->app->path->path('helpers:fields'), 'fields');
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
     * @param       $method
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
        /** @var AppParameterForm $form */
        $form = $this->app->parameterform->create();

        $params = array();

        $class = new ReflectionClass($this);
        while ($class !== false) {
            $xmlPath = preg_replace('#\.php$#i', '.xml', $class->getFileName());
            if (file_exists($xmlPath)) {
                $params[] = $xmlPath;
            }

            $class = $class->getParentClass();
        }

        $params = array_reverse($params);

        // trigger configparams event
        $event  = $this->app->event->create($this, 'cart-element:configparams')->setReturnValue($params);
        $params = $this->app->event->dispatcher->notify($event)->getReturnValue();

        // skip if there are no config files
        $params = array_filter($params);
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
        $this->app->jbevent->fire($this, 'cart-element:configform', compact('form'));

        $fieldsPath = JPath::clean($this->getPath() . '/fields');
        if (is_dir($fieldsPath)) {
            $form->addElementPath($fieldsPath);
        }

        return $form;
    }

    /**
     * Get elements xml meta data
     * @param null $key
     * @return bool
     */
    public function getMetaData($key = null)
    {

        if (!isset($this->_metaData)) {
            $data = array();
            $xml  = $this->loadXML();

            if (!$xml) {
                return false;
            }

            $data['type']         = $xml->attributes()->type ? (string)$xml->attributes()->type : 'Unknown';
            $data['group']        = $xml->attributes()->group ? (string)$xml->attributes()->group : 'Unknown';
            $data['hidden']       = $xml->attributes()->hidden ? (string)$xml->attributes()->hidden : 'false';
            $data['core']         = $xml->attributes()->core ? (string)$xml->attributes()->core : 'false';
            $data['system-tmpl']  = $xml->attributes()->{'system-tmpl'} ? (string)$xml->attributes()->{'system-tmpl'} : 'false';
            $data['trusted']      = $xml->attributes()->trusted ? (string)$xml->attributes()->trusted : 'false';
            $data['orderable']    = $xml->attributes()->orderable ? (string)$xml->attributes()->orderable : 'false';
            $data['name']         = JText::_((string)$xml->name);
            $data['creationdate'] = $xml->creationDate ? (string)$xml->creationDate : 'Unknown';
            $data['author']       = $xml->author ? (string)$xml->author : 'Unknown';
            $data['copyright']    = (string)$xml->copyright;
            $data['authorEmail']  = (string)$xml->authorEmail;
            $data['authorUrl']    = (string)$xml->authorUrl;
            $data['version']      = (string)$xml->version;
            $data['description']  = JText::_((string)$xml->description);

            $this->_metaData = $this->app->data->create($data);
        }

        return $key == null ? $this->_metaData : $this->_metaData->get($key);
    }

    /**
     * Retrieve Element XML file info
     * @return SimpleXMLElement
     */
    public function loadXML()
    {
        if (!isset($this->_xmlData)) {

            $type  = $this->getElementType();
            $group = $this->getElementGroup();

            $this->_xmlData = simplexml_load_file($this->app->path->path("cart-elements:$group/$type/$type.xml"));
        }

        return $this->_xmlData;
    }

    /**
     * Gets the controle name for given name
     * @param      $name
     * @param bool $array
     * @return string
     */
    public function getControlName($name, $array = false)
    {
        return $this->_namespace . '[' . $this->identifier . '][' . $name . ']' . ($array ? '[]' : '');
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

    /**
     * Check if element is system
     * @return bool
     */
    public function isSystemTmpl()
    {
        $systemTmpl = strtolower($this->getMetaData('system-tmpl'));
        if ($systemTmpl == 'true') {
            return true;
        }

        return false;
    }

    /**
     * Check if element is core
     * @return bool
     */
    public function isCore()
    {
        $core = strtolower($this->getMetaData('core'));
        if ($core == 'true') {
            return true;
        }

        return false;
    }

    /**
     * Check if element is core
     * @return bool
     */
    public function isHidden()
    {
        $hidden = strtolower($this->getMetaData('hidden'));
        if ($hidden == 'true' || $hidden == '1') {
            return true;
        }

        return false;
    }

    /**
     * Renders the element in submission
     * @param array $params
     * @return string|void
     */
    public function renderSubmission($params = array())
    {
        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'value'  => $this->get('value'),
                'order'  => $this->getOrder(),
            ));
        }

        return null;
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $params = $this->app->data->create($params);
        $value  = $this->app->data->create($value);

        return array(
            'value' => $this->app->validator
                ->create('textfilter', array(
                    'required' => (int)$params->get('required'),
                    'trim'     => true,
                ))
                ->clean($value->get('value')),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        $name = $this->config->get('name');

        if (empty($name)) {
            $retult = $this->getMetaData('name');
        } else {
            $retult = JText::_($name);
        }

        $retult = JString::trim($retult);

        return $retult;
    }

    /**
     * @return JSONData
     */
    public function getOrderData()
    {
        return $this->app->data->create(array(
            'data'   => $this->data(),
            'config' => (array)$this->config,
        ));
    }

    /**
     * @param null $default
     * @return mixed
     */
    public function getDescription($default = null)
    {
        return JText::_($this->config->get('description', $default));
    }

    /**
     * @param bool $uniq
     * @return string
     */
    public function htmlId($uniq = false)
    {
        $id = 'jbcart-' . $this->identifier;

        if ($uniq) {
            return $this->app->jbstring->getId($id);
        }

        return $id;
    }

    /**
     * @param string $methodName
     * @param array  $params
     * @return mixed
     */
    public function getAjaxUrl($methodName = 'ajax', array $params = array())
    {
        $urlParams = array(
            'order_id' => $this->getOrder()->id,
            'group'    => $this->getElementGroup(),
            'element'  => $this->identifier,
        );

        return $this->app->jbrouter->elementOrder($methodName, $urlParams, $params);
    }

    /**
     * @param $attrs
     * @return mixed
     */
    protected function _attrs($attrs)
    {
        return $this->app->jbhtml->buildAttrs($attrs);
    }

    /**
     * @param string $positon
     */
    public function saveConfig($positon = JBCart::DEFAULT_POSITION)
    {
        $config = JBModelConfig::model();
        $list   = $config->getGroup('cart.' . $this->getElementGroup());

        $list[$positon][$this->identifier] = (array)$this->config;

        $config->setGroup('cart.' . $this->getElementGroup(), $list);
    }

}

/**
 * Class JBCartElementException
 */
class JBCartElementException extends AppException
{
}