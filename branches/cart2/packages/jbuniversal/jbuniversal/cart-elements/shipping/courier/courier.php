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
 * Class JBCartElementShippingCourier
 */
class JBCartElementShippingCourier extends JBCartElementShipping
{

    /**
     * @param float $sum
     * @param string $currency
     * @param JBCartOrder $order
     * @return float
     */
    public function modify($sum, $currency, JBCartOrder $order)
    {
        return $sum + 500;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->data()->get('status', 'undefined');
    }

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {
        return 'Наши курьеры рулят! ' . $this->identifier;
    }

    public function getRate()
    {
        return 500;
    }


}
