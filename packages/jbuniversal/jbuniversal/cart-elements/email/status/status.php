<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementEmailStatus
 */
class JBCartElementEmailStatus extends JBCartElementEmail
{

    /**
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $status = $this->_getStatus();
        return !empty($status);
    }

    /**
     * @return JBCartElementStatus
     */
    protected function _getStatus()
    {
        $ordertype = $this->config->get('ordertype', 'main');
        $order     = $this->getOrder();

        if ($ordertype == 'main') {
            return $order->getStatus();

        } else if ($ordertype == 'payment') {
            return $order->getPaymentStatus();

        } else if ($ordertype == 'shipping') {
            return $order->getShippingStatus();
        }

        return null;
    }

}
