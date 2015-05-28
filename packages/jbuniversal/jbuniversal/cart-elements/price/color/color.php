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

App::getInstance('zoo')->loader->register('JBCartElementPriceOption', 'cart-elements:price/option/option.php');

/**
 * Class JBCartElementPriceColor
 */
class JBCartElementPriceColor extends JBCartElementPriceOption
{
    /**
     * @type JBColorHelper
     */
    protected $_jbcolor;

    /**
     * Constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbcolor = $this->app->jbcolor;
    }

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        return $this->getValue();
    }

    /**
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return $this->renderEditLayout($layout, array(
                'data'  => $this->_parseOptions(),
                'name'  => $this->getControlName('value'),
                'value' => $this->getValue()
            ));
        }

        return null;
    }

    /**
     * @param array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout($params->get('layout', 'color') . '.php')) {
            return $this->renderLayout($layout, array(
                'width'     => (int)$params->get('height', 26) . 'px',
                'height'    => (int)$params->get('width', 26) . 'px',
                'value'     => $this->getValue(),
                'name'      => $this->getRenderName('value'),
                'data'      => $this->_getOptions(true),
                'dataColor' => $this->_getColors()
            ));
        }

        return null;
    }

    /**
     * Parse options from element config
     * @param  bool $label - add option with no value
     * @return array
     */
    protected function _parseOptions($label = false)
    {
        $colors = $this->_jbcolor->getColors($this->config->get('options'), $this->config->get('path', 'images'));

        return $colors;
    }

    /**
     * Get options for simple element
     * @param  bool $label - add option with no value
     * @return array
     */
    protected function _getOptions($label = true)
    {
        $colors = $this->_parseOptions();
        if (!$this->showAll) {
            $selected = $this->_jbprice->elementOptions($this->identifier);
            $colors   = array_intersect_key($colors, $selected);
        }

        // convert to custom view
        $options = array();
        foreach (array_keys($colors) as $color) {
            $options[$color] = JString::ucfirst($color);
        }

        if ($label && count($options)) {
            $options[''] = $this->getLabel();
            ksort($options);
        }

        return $options;
    }

    /**
     * @param  array $colors
     * @return mixed
     */
    protected function _getColors($colors = array())
    {
        $colors = $this->_parseOptions();
        if (!$this->showAll) {
            $selected = $this->_jbprice->elementOptions($this->identifier);
            $colors   = array_intersect_key($colors, $selected);
        }

        return $colors;
    }

    /**
     * Check if option isset in element
     * @param $value
     * @return bool
     */
    public function hasOption($value)
    {
        $colors = $this->_getColors();

        return (array_key_exists($value, $colors));
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->colors();
        $this->js('jbassets:js/widget/colors.js');
        $this->less('jbassets:less/widget/colors.less');

        return parent::loadAssets();
    }

    /**
     * Load config assets
     * @return self
     */
    public function loadConfigAssets()
    {
        JHtml::_('behavior.colorpicker');
        return parent::loadConfigAssets();
    }

    /**
     * Clean data before bind into element
     * @param array  $data
     * @param string $key
     * @return $this
     */
    public function bindData($data = array(), $key = 'value')
    {
        if (array_key_exists('value', $data)) {
            $data['value'] = $this->_jbcolor->clean($data['value']);

            parent::bindData($data);
        }

        return $this;
    }
}
