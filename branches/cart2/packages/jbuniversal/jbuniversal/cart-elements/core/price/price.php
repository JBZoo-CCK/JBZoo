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
    /**
     * @var JBModelConfig
     */
    protected $_jbconfig;

    /**
     * @var JBHtmlHelper
     */
    protected $_jbhtml;

    /**
     * @var ElementJBPriceAdvance
     */
    protected $_jbprice;

    /**
     * Constructor
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbhtml   = $app->jbhtml;
        $this->_jbconfig = JBModelConfig::model();
    }

    /**
     * @return mixed
     */
    abstract function edit();

    /**
     * @param  array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);
        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'field'  => $this->renderFieldByType()
            ));
        }

        return false;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $params = array();
        $config = $this->config;
        $data   = $this->app->data->create($config->get('data'));

        $params['identifier'] = $config->get('related_identifier');
        $params['basic']      = (int)$data->get('basic', 0);
        $params['data']       = $data->get('params');

        return $this->app->data->create($params);
    }

    /**
     * @param null $identifier
     * @return array
     */
    public function getAllData($identifier = null)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return $this->_jbprice->getAllParamData($identifier);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getBasic($key, $default = null)
    {
        $basic  = $this->app->data->create($this->_jbprice->getBasicData());
        $params = $this->app->data->create($basic->get('params'));

        $value = $basic->get($key);
        if (empty($value)) {
            $value = $params->get($key, $default);
        }

        return $value;
    }

    /**
     * @param ElementJBPriceAdvance $object
     */
    public function setJBPrice(ElementJBPriceAdvance $object)
    {
        static $add = false;

        if (!$add) {
            $this->_jbprice = $object;
        }

    }

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getValue($key, $default = null)
    {
        $params = $this->getParams();
        $param  = $this->app->data->create($params->get('data'));

        return $param->get($key, $default);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        $priceparams = $this->_jbconfig->getGroup('cart.priceparams');

        $list = $priceparams->get('list');

        return $this->app->data->create($list);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getName($key = 'value')
    {
        $params = $this->getParams();

        $name = $this->getParamName($params->get('identifier'), $key, $params->get('variant', 0));
        if ((int)$params->get('basic', 0)) {
            $name = $this->getBasicName($params->get('identifier'), $key);
        }

        return $name;
    }

    /**
     * @param null $identifier
     * @param $name
     * @return string
     */
    public function getBasicName($identifier = null, $name)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][basic][params][{$name}]";
    }

    /**
     * @param null $identifier
     * @param $name
     * @param  int $index
     * @return string
     */
    public function getParamName($identifier = null, $name, $index = 0)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][variations][{$index}][params][{$name}]";
    }

    /**
     * @return mixed
     */
    protected function _getOptions()
    {
        $data = $this->config->get('options', array());

        return $data;
    }

    /**
     * @return array|null
     */
    protected function _renderOptions()
    {
        $options = $this->_getOptions();
        $result  = array();

        if (!empty($options)) {
            $options = explode("\n", $options);
            foreach ($options as $key => $value) {
                list($name, $value) = explode('||', $value);
                $result[$key]['name']  = JString::trim($name);
                $result[$key]['value'] = JString::strtolower(JString::trim($value));
            }

            return $result;
        }

        return null;
    }

    /**
     * @param bool $params
     * @return mixed
     */
    public function getDefaultVariantData($params = false)
    {
        $defaultVariant = $this->_jbprice->config->get('default');
        $defaultVariant = $this->app->data->create($defaultVariant);

        if ($params) {
            $variantParmas = $this->app->data->create($defaultVariant->get('params'));

            return $variantParmas;
        }

        return $defaultVariant;
    }

    /**
     * @return null
     */
    protected function renderFieldByType()
    {
        $values = $this->_renderOptions();
        $option = array();

        if (!empty($values)) {
            $type = $this->getElementType();
            $attr = $this->_jbhtml->buildAttrs(array(
                'class' => 'jsParam'
            ));

            foreach ($values as $options) {
                $option[] = $this->app->html->_('select.option', $options['value'], $options['name']);
            }

            return $this->_jbhtml->$type($option, $this->getName(), $attr, $attr);
        }

        return null;
    }

}

/**
 * Class JBCartElementPriceException
 */
class JBCartElementPriceException extends JBCartElementException
{
}
