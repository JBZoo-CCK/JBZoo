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
 * Class JBCartElementModifierItemPrice
 */
abstract class JBCartElementModifierItemPrice extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_MODIFIER_ITEM_PRICE;

    /**
     * @param JBCartValue $summa
     * @param Item       $item
     * @return JBCartValue
     */
    //abstract public function modify(JBCartValue $summa, Item $item = null);

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val(0);
    }

}

/**
 * Class JBCartElementModifierItemPriceException
 */
class JBCartElementModifierItemPriceException extends JBCartElementException
{
}
