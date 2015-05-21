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
 * Class JBCartElementHookJBDumpLog
 */
class JBCartElementHookJBDumpLog extends JBCartElementHook
{

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return class_exists('jbdump');
    }

    /**
     * @param $params
     */
    public function notify($params = array())
    {
        $order     = $this->getOrder();
        $eventName = $this->getEvent()->getName();
        $message   = $eventName . '  / ' . $this->getName();

        if ($order && $order->id) {
            $message .= ' (id:' . $order->id . ')';

        } else if (!empty($order)) {
            $message .= ' (id:0)';

        } else if (!empty($order)) {
            $message .= ' (null)';
        }

        jbdump::log($message);
    }

}
