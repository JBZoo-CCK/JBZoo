<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
