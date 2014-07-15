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

        $params['identifier'] = $this->config->get('related_identifier');
        $params['key']        = $this->config->get('key');
        $params['variant']    = $this->config->get('variant', 0);
        $params['selected']   = $this->config->get('selected', array());
        $params['config']     = $this->config->get('config', array());
        $params['basic']      = $this->config->get('basic', 0);
        $params['basicData']  = $this->config->get('basicData', array());

        return $this->app->data->create($params);
    }

    public function getPosition()
    {
        $identifier = $this->config->get('related_identifier');

        return $this->app->jbhtml->hidden("elements[{$identifier}][variations][0][params][variant]", $this->config->get('variant', 0), 'class="hidden-variant"');
    }

    public function getBasic($key = '_sku', $default = null)
    {
        $key = JString::trim($key, '_');

        $params = $this->getParams();
        $basic  = $this->app->data->create($params->get('basicData'));

        return $basic->get($key, $default);
    }

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $result  = false;
        $options = $this->config->get('options', array());
        if (!empty($options)) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getValue($key = '_sku', $default = null)
    {
        $params = $this->getParams();
        $value  = $this->config->get($key, $default);

        if ((int)$params->get('basic', 0)) {
            $value = $this->getBasic($key, $default);
        }

        if (is_array($value)) {
            return !empty($value[$key]) ? $value[$key] : null;
        }

        return (!empty($value) ? $value : $default);
    }

    public function getBasicTmpl()
    {
        $params  = $this->getParams();
        $content = null;

        if ((int)$params->get('basic', 0) && $layout = $this->getLabelLayout('label.php')) {

            if ($default = $this->getLayout('default.php')) {
                $content = self::renderLayout($default, array(
                    'params' => $params,
                ));
            }

            return self::renderLayout($layout, array(
                'params'  => $params,
                'type'    => JString::strtolower($this->getElementType()),
                'content' => $content
            ));
        }

        return null;
    }

    /**
     * Get element layout path and use override if exists
     * @param null $layout
     * @return string
     */
    public function getLabelLayout($layout = null)
    {
        $group = 'core';
        $type  = 'price';

        // set default
        if ($layout == null) {
            $layout = "{$type}.php";
        }

        // find layout
        return $this->app->path->path("cart-elements:{$group}/{$type}/tmpl/{$layout}");
    }

    public function getConfig()
    {
        $priceparams = $this->_jbconfig->getGroup('cart.priceparams');

        $list = $priceparams->get('list');

        return $this->app->data->create($list);
    }

    public function getName($key = 'value')
    {
        $params = $this->getParams();

        $name = $this->getParamName($params->get('identifier'), $key, $params->get('variant', 0));
        if ((int)$params->get('basic', 0)) {
            $name = $this->getBasicName($params->get('identifier'), $key);
        }

        return $name;
    }

    public function getBasicName($identifier = null, $name)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][basic][{$name}]";
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
                list($name, $value) = explode('|', $value);
                $result[$key]['name']  = JString::trim($name);
                $result[$key]['value'] = JString::trim($value);
            }

            return $result;
        }

        return null;
    }

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
