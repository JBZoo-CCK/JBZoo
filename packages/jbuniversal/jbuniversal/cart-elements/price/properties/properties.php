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
        $width  = $this->getValue(true, 'width');
        $length = $this->getValue(true, 'length');
        $height = $this->getValue(true, 'height');

        if (!empty($width) || !empty($length) || !empty($height)) {
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
        $this->app->jbassets->less('cart-elements:price/properties/assets/less/edit.less');

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout, array(
                'width'  => $this->get('width'),
                'height' => $this->get('height'),
                'length' => $this->get('length')
            ));
        }

        return null;
    }

    /**
     * @param array $params
     * @return array|mixed|null|string|void
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout('properties.php')) {
            return self::renderLayout($layout, array(
                'width'  => $this->get('width'),
                'height' => $this->get('height'),
                'length' => $this->get('length')
            ));
        }

        return null;
    }

    /**
     * Get elements value
     * @param array|string $key      Array key.
     * @param mixed        $default  Default value if data is empty.
     * @param bool         $toString A string representation of the value.
     * @return mixed|string
     */
    public function getValue($toString = false, $key = array('height', 'length', 'width'), $default = null)
    {
        if (is_string($key) && $toString) {
            $value = parent::getValue($toString, $key, $default);

        } elseif ($toString) {
            $value = call_user_func_array('parent::getValue', $key);

        } else {
            $callable = array($this, 'getValue');
            array_walk($key, function ($_val) use (&$value, $callable) {
                $value[$_val] = call_user_func($callable, array(true, $_val));
            });
        }

        return $value;
    }
}
