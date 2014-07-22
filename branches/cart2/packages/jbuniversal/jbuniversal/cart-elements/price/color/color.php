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
     * @return mixed|null|string
     */
    public function edit()
    {
        $params     = $this->getParams();
        $type       = $this->getInputType();
        $colorItems = $this->getColors();

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params'     => $params,
                'type'       => $type,
                'colorItems' => $colorItems,
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
     * @return mixed
     */
    public function getColors()
    {
        $colors = explode("\n", $this->config->get('options'));

        $colorItems = $this->app->jbcolor->getColors($colors, $this->config->get('path', 'images'));

        return $colorItems;
    }

    /**
     * @param null $identifier
     * @param $name
     * @param int $index
     * @return string
     */
    public function getParamName($identifier = null, $name, $index = 0)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][variations][{$index}][params][{$this->identifier}][{$name}]";
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

        return "elements[{$identifier}][basic][params][{$this->identifier}][{$name}]";
    }
}
