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
 * Class JBCartElementmodifierItemActivate
 */
class JBCartElementModifierItemActivate extends JBCartElementModifierItem
{
    /**
     * @param $order
     * @param $params
     */
    public function notify($order, $params)
    {
        if (class_exists('jbdump')) {
            jbdump::trace();
            jbdump::log($params, $order->id);
            dump($params, 0, 'params');
            dump($order->id, 1, 'id');
        }

        // noop
    }

}
