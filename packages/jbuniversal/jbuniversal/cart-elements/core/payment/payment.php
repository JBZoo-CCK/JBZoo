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
 * Class JBCartElementPayment
 */
abstract class JBCartElementPayment extends JBCartElement
{
    protected $_namespace = JBCartOrder::ELEMENT_TYPE_PAYMENT;

    /**
     * @param App $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->registerCallback('paymentCallback');
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->data()->get('status', 'undefined');
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return 0;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        $shipping = JBModelConfig::model()->get('cart.config.default_payment');

        return $this->identifier == $shipping;
    }

    /**
     * @return null
     */
    public function getRedirectUrl()
    {
        return null;
    }

    /**
     * @param float $sum
     * @param string $currency
     * @param JBCartOrder $order
     * @return float
     */
    abstract public function modify($sum, $currency, JBCartOrder $order);

    /**
     * Plugin even triggered when the payment plugin notifies for the transaction
     * @param array $params The data received
     * @return null|void
     */
    public function paymentCallback($params = array())
    {

    }

    /**
     * @param array $data
     * @return string
     */
    public function fail($data = array())
    {
        return $this->renderLayout('fail', $data);
    }

    /**
     * @param array $data
     * @return string
     */
    public function success($data = array())
    {
        return $this->renderLayout('success', $data);
    }

    /**
     * @param $name
     * @param bool $array
     * @return string|void
     */
    public function getControlName($name, $array = false)
    {
        return $this->_namespace . '[' . $name . ']';
    }

}

/**
 * Class JBCartElementPaymentException
 */
class JBCartElementPaymentException extends JBCartElementException
{
}
