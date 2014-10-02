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
    protected $_namespace = JBCart::ELEMENT_TYPE_PAYMENT;

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
        $default = JBCart::getInstance()->getDefaultStatus(JBCart::STATUS_PAYMENT);
        return $this->get('status', $default->getCode());
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
        $shipping = JBModelConfig::model()->get('default_payment', null, 'cart.config');

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

    /**
     * Change payment status and fire event
     * @param $newStatus
     */
    public function setStatus($newStatus)
    {
        $oldStatus = $this->getStatus();
        if ($oldStatus && $oldStatus != $newStatus) {
            $this->app->event->dispatcher->notify($this->app->event->create($this->getOrder(), 'basket:paymentStatus', compact('oldStatus', 'newStatus')));
        }

        $this->set('status', $newStatus);
    }

    /**
     * @return JSONData|void
     */
    public function getOrderData()
    {
        $data = parent::getOrderData();
        $data->set('status', $this->getStatus());

        return $data;
    }

}

/**
 * Class JBCartElementPaymentException
 */
class JBCartElementPaymentException extends JBCartElementException
{
}
