<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
