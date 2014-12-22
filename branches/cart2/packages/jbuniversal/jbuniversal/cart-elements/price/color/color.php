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
 * Class JBCartElementPriceColor
 */
class JBCartElementPriceColor extends JBCartElementPrice
{
    /**
     * Check if element has value
     * @param array|JSONData $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $selected = $this->_jbprice->elementOptions($this->identifier);
        if (!empty($selected)) {
            return true;
        }

        return false;
    }

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        $value = $this->getValue();

        return $value;
    }

    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        $type       = $this->getInputType();
        $colorItems = $this->getColors();

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'type'       => $type,
                'colorItems' => $colorItems,
            ));
        }

        return null;
    }

    /**
     * @param array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $type   = $this->getInputType();
        $height = (int)$params->get('height', 26);
        $width  = (int)$params->get('width', 26);

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params'     => $params,
                'type'       => $type,
                'width'      => $width,
                'height'     => $height,
                'colorItems' => $this->getOptions()
            ));
        }

        return null;
    }

    /**
     * Get type for input
     * @return string
     */
    public function getInputType()
    {
        $type = (boolean)$this->config->get('multiplicity', 1);
        if (!$type) {
            return 'radio';
        }

        return 'checkbox';
    }

    /**
     * Parse options from element config
     * @param  bool $label - add option with no value
     * @return array
     */
    public function parseOptions($label = false)
    {
        $options = explode("\n", $this->config->get('options'));
        $colors  = $this->app->jbcolor->getColors($options, $this->config->get('path', 'images'));

        return $colors;
    }

    /**
     * Get options for simple element
     * @param  bool $label - add option with no value
     * @return mixed
     */
    public function getOptions($label = true)
    {
        $jbPrice = $this->_jbprice;
        $colors  = $this->parseOptions();

        if (!$this->hasOptions() || (int)$jbPrice->config->get('only_selected', 1)) {
            $options = $jbPrice->elementOptions($this->identifier);
            $colors  = array_intersect_key($colors, $options);
        }

        return $colors;
    }

    /**
     * @param  array $colors
     * @return mixed
     */
    public function getColors($colors = array())
    {
        if (empty($colors)) {
            $colors = explode("\n", $this->config->get('options'));
        }

        return $this->app->jbcolor->getColors($colors, $this->config->get('path', 'images'));
    }

    /**
     * Get elements value
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getValue($key = 'value', $default = null)
    {
        return $this->get($key, $default);
    }

    /**
     * Check if option isset in element
     *
     * @param $value
     *
     * @return bool
     */
    public function issetOption($value)
    {
        $colors = $this->getColors();
        if (array_key_exists($value, $colors)) {
            return true;
        }

        return false;
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->colors();

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
}
