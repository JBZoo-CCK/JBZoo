<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementModifierOrderPriceAddVAT
 */
class JBCartElementModifierOrderPriceAddVAT extends JBCartElementModifierOrderPrice
{
    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val($this->config->get('rate'));
    }

}
