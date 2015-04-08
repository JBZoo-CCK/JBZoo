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
 * Class JBPriceFilterElementDiscountJQueryUI
 */
class JBPriceFilterElementDiscountJQueryUI extends JBPriceFilterElementDiscount
{
    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        $options = $this->_getValues();
        unset($this->_attrs['id']);

        return $this->_html->buttonsJQueryUI(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId('discount', true)
        );
    }
}
