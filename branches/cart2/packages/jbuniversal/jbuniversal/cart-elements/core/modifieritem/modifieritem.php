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
 * Class JBCartElementModifierItem
 */
abstract class JBCartElementModifierItem extends JBCartElement
{

    protected $_namespace = JBCart::ELEMENT_TYPE_MODIFIER_ITEM;

    /**
     * @param $order
     * @param $params
     */
    public function notify($order, $params)
    {
        // noop
    }

}

/**
 * Class JBCartElementModifierItemException
 */
class JBCartElementModifierItemException extends JBCartElementException
{
}
