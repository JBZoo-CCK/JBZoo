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
