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
 * Class JBCartElementModifierPriceAddVAT
 */
class JBCartElementModifierPriceAddVAT extends JBCartElementModifierPrice
{
    /**
     * @param float $sum
     * @param string $currency
     * @param JBCartOrder $order
     * @return float
     */
    public function modify($sum, $currency, JBCartOrder $order)
    {
        $rate = (float)$this->getRate() + 100;
        return $this->app->jbmoney->calc($sum, $currency, $rate, '%');
    }

    /**
     * @return int|string
     */
    public function getRate()
    {
        $percent = $this->config->get('percent', 18);
        return $percent . '%';
    }

}
