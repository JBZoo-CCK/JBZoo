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
 * Class JBCartElementModifierPrice
 */
abstract class JBCartElementModifierPrice extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_MODIFIERPRICE;

    /**
     * @param float $sum
     * @param string $currency
     * @param JBCartOrder $order
     * @return float
     */
    abstract public function modify($sum, $currency, JBCartOrder $order);

    /**
     * @return int
     */
    public function getRate()
    {
        return 0;
    }

}

/**
 * Class JBCartElementModifierItemException
 */
class JBCartElementModifierPriceException extends JBCartElementException
{
}
