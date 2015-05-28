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
 * Class JBCartElementModifierItemPriceMultiply
 */
class JBCartElementModifierItemPriceMultiply extends JBCartElementModifierItemPrice
{
    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        if ($this->_isValid()) {
            $rate = $this->app->jbvars->number($this->config->get('rate', 0));
            if ($rate) {
                $multiplier = ($rate - 1) * 100;

                return $this->_order->val($multiplier, '%');
            }
        }

        return $this->_order->val(0, '%');
    }
}
