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
 * Class JBCartElementPriceMargin
 */
class JBCartElementPriceMargin extends JBCartElementPrice
{
    /**
     * Check if element has value
     * @param AppData|array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return false;
    }

    /**
     * @param  array $params
     * @return bool
     */
    public function hasFilterValue($params = array())
    {
        return false;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout, array(
                'value'   => $this->get('value', ''),
                'message' => JText::sprintf('JBZOO_JBPRICE_CALC_PARAM_CANT_USE', '<strong>' . $this->getElementType() . '</strong>')
            ));
        }

        return null;
    }

    /**
     * @param  array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        return null;
    }

    /**
     * Get elements value
     * @param string $key      Array key.
     * @param mixed  $default  Default value if data is empty.
     * @param bool   $toString A string representation of the value.
     * @return mixed|string
     */
    public function getValue($toString = false, $key = 'value', $default = null)
    {
        $value = parent::getValue($toString, $key, $default);
        $value = $this->clearSymbols($value, array('-', '+'));

        if ($toString) {
            return $value;
        }

        return JBCart::val($value);
    }
}
