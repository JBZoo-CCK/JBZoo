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

/**
 * Class JBCartElementPriceProperties
 */
class JBCartElementPriceProperties extends JBCartElementPrice
{
    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $length = $this->getValue(true, 'length');
        $height = $this->getValue(true, 'height');
        $width  = $this->getValue(true, 'width');

        if (!empty($length) || !empty($height) || !empty($width)) {
            return true;
        }

        return false;
    }

    /**
     * Render element in jbprice admin
     * @param array $params
     * @return bool|mixed|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout, array(
                'height' => $this->getValue(true, 'height'),
                'length' => $this->getValue(true, 'length'),
                'width'  => $this->getValue(true, 'width')
            ));
        }
    }

    /**
     * @param array $params
     * @return array|mixed|null|string|void
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout('properties.php')) {
            return self::renderLayout($layout, array(
                'height' => $this->getValue(true, 'height'),
                'length' => $this->getValue(true, 'length'),
                'width'  => $this->getValue(true, 'width')
            ));
        }

        return null;
    }

    /**
     * Get elements value
     * @param array $key      Array key.
     * @param mixed $default  Default value if data is empty.
     * @param bool  $toString A string representation of the value.
     * @return mixed|string
     */
    public function getValue($toString = false, $key = array('height', 'length', 'width'), $default = null)
    {
        if ($toString && is_string($key)) {
            $value = parent::getValue($toString, $key, $default);
        } else {
            $value = call_user_func_array('parent::getValue', $key);
        }

        return $value;
    }
}
