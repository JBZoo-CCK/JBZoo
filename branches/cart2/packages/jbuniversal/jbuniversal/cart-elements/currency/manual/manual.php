<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class JBCartElementCurrencyManual
 */
class JBCartElementCurrencyManual extends JBCartElementCurrency
{
    /**
     * @param null $currency
     * @return array|void
     */
    public function _loadData($currency = null)
    {
        $code = $this->getCode();
        $rate = $this->config->get('rate', 1);

        if ($currency && $currency != $code) {
            return array();
        }

        if ($code && $rate > 0) {
            return array($code => $rate);
        }

        return array();
    }

}
