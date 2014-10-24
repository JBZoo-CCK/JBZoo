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
     *
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        //TODO make checking method like $this->getAllOptions()
        return true;
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
        $params = $this->app->data->create($params);
        $type   = $this->getInputType();
        $height = (int)$params->get('height', 26);
        $width  = (int)$params->get('width', 26);

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params'     => $params,
                'type'       => $type,
                'width'      => $width,
                'height'     => $height,
                'colorItems' => $this->getColors()
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
     * @param array $selected
     *
     * @return mixed
     */
    public function getColors($selected = array())
    {
        $colors = explode("\n", $this->config->get('options'));
        $result = array();
        $data   = array();

        $colorItems = $this->app->jbcolor->getColors($colors, $this->config->get('path', 'images'));

        foreach ($colorItems as $key => $value) {
            $result[$key] = $value;
        }


        if (!empty($selected)) {
            foreach ($selected as $color) {
                $key = $this->app->string->sluggify(JString::trim($color));

                if (!empty($result[$key])) {
                    $data[$key] = $color;
                }
            }

            return $data;
        }

        return $result;
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
