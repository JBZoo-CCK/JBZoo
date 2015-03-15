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
 * Class JBCartElementCurrencyPercent
 */
class JBCartElementCurrencyPercent extends JBCartElementCurrency
{
    /**
     * @param null $currency
     * @return array|void
     */
    public function _loadData($currency = null)
    {
        return array();
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return JBCartValue::PERCENT;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return JText::_('JBZOO_CART_CURRENCY_PERCENT');
    }

    public function checkCurrency($currency)
    {
        return $currency == $this->getCode();
    }

}
