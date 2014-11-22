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
    /**
     * @var JBRouterHelper
     */
    protected $_jbrouter;

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney;

    /**
     * @var JBRequestHelper
     */
    protected $_jbrequest;

    /**
     * @var string
     */
    protected $_namespace = JBCart::ELEMENT_TYPE_PAYMENT;

    /**
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbrouter  = $this->app->jbrouter;
        $this->_jbmoney   = $this->app->jbmoney;
        $this->_jbrequest = $this->app->jbrequest;

        $this->registerCallback('paymentCallback');
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $default = JBCart::getInstance()->getDefaultStatus(JBCart::STATUS_PAYMENT);

        $curStatus = $this->get('status');
        if (empty($curStatus)) {
            $curStatus = $default->getCode();
        }

        return $curStatus;
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


    public function getSuccessMessage()
    {
        return 'JBZOO_CART_PAYMENT_REDIRECT';
    }

    /**
     * @param float       $sum
     * @param string      $currency
     * @param JBCartOrder $order
     *
     * @return float
     */
    public function modify($sum, $currency, JBCartOrder $order)
    {
        return $sum;
    }

    /**
     * Plugin even triggered when the payment plugin notifies for the transaction
     *
     * @param array $params The data received
     *
     * @return null|void
     */
    public function isValid($params = array())
    {
        return false;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function fail($data = array())
    {
        return $this->renderLayout('fail', $data);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function success($data = array())
    {
        return $this->renderLayout('success', $data);
    }

    /**
     * @param      $name
     * @param bool $array
     *
     * @return string|void
     */
    public function getControlName($name, $array = false)
    {
        return $this->_namespace . '[' . $name . ']';
    }

    /**
     * Change payment status and fire event
     *
     * @param $newStatus
     */
    public function setStatus($newStatus)
    {
        $oldStatus = (string)$this->getStatus();
        $newStatus = (string)$newStatus;

        $isChanged = $oldStatus // is not first set on order creating
            && $oldStatus != JBCartStatusHelper::UNDEFINED // old is not empty
            && $oldStatus != $newStatus; // is really changed

        if ($isChanged) {

            $this->app->event->dispatcher->notify($this->app->event->create(
                $this->getOrder(),
                'basket:paymentStatus',
                array(
                    'oldStatus' => $oldStatus,
                    'newStatus' => $newStatus,
                )
            ));

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

    /**
     * Check is debug mode enabled
     * @return int
     */
    public function isDebug()
    {
        return (int)$this->config->get('debug', 0);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function renderSubmission($params = array())
    {
        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
            ));
        }

        return false;
    }

    /**
     * @return null
     */
    public function renderPaymentForm()
    {
        return null;
    }

    /**
     * @param array $request
     * @return bool
     */
    public function validatePaymentForm($request = array())
    {
        return true;
    }

    /**
     * @return null
     */
    public function actionPaymentForm()
    {
        return null;
    }

    /**
     * Set success payment status to order
     */
    public function setSuccess()
    {
        $order   = $this->getOrder();
        $payment = $order->getPayment();
        $cart    = JBCart::getInstance();

        if ($payment) {
            $successStatus = $cart->getPaymentSuccess();
            $payment->setStatus($successStatus);
            JBModelOrder::model()->save($order);
        }
    }

    /**
     * Render response for merchant
     */
    public function renderResponse()
    {
        jexit('OK' . $this->getOrderId());
    }

    /**
     * @return float
     */
    public function getOrderSumm()
    {
        $order  = $this->getOrder();
        $defCur = $this->_jbmoney->getDefaultCur();
        $summ   = $this->_jbmoney->convert($defCur, $defCur, $order->getTotalSum(false));
        $summ   = round($summ, 2);

        return $summ;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        $order = $this->getOrder();
        if (!empty($order)) {
            return $this->getOrder()->id;
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->_jbmoney->getDefaultCur();
    }

    /**
     * Detect order id from merchant's robot request
     * @return int
     */
    public function getRequestOrderId()
    {
        return -1;
    }

    /**
     * Detect order id from merchant's robot request
     * @return int
     */
    public function getRequestOrderSum()
    {
        return -1;
    }

    /**
     * @return string
     */
    public function getORderDescription()
    {
        return 'Order #' . $this->getOrderId() . ' from ' . JUri::getInstance()->getHost();
    }

}

/**
 * Class JBCartElementPaymentException
 */
class JBCartElementPaymentException extends JBCartElementException
{
}
