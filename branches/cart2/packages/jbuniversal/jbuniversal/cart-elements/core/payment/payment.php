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
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @return JBCartElementStatus
     */
    public function getStatus()
    {
        $default = JBCart::getInstance()->getDefaultStatus(JBCart::STATUS_PAYMENT);

        $curStatus = $this->get('status');
        if (empty($curStatus)) {
            $curStatus = $default;
        }

        if (!is_object($curStatus)) {

            /** @var JBCartStatusHelper $jbstatus */
            $jbstatus = $this->app->jbcartstatus;

            $status = $jbstatus->getByCode($curStatus, JBCart::STATUS_PAYMENT, $this->getOrder());
            if (!empty($status)) {
                return $status;
            }

            // if not found in current configs
            // TODO get status info from order params
            $unfound = $jbstatus->getUndefined();
            $unfound->config->set('code', $curStatus);
            $unfound->config->set('name', $curStatus);

            return $unfound;
        }

        return $curStatus;
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val(0);
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        $config = JBModelConfig::model();

        $currentPayment = $config->get('default_payment', null, 'cart.config');

        $paymentList = $config->get(JBCart::DEFAULT_POSITION, array(), 'cart.' . JBCart::CONFIG_PAYMENTS);
        if (empty($currentPayment) || !isset($paymentList[$currentPayment])) {
            reset($paymentList);
            $currentPayment = key($paymentList);
        }

        return $this->identifier == $currentPayment;
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
     * @param JBCartValue $summa
     * @return JBCartValue
     */
    public function modify(JBCartValue $summa)
    {
        if ($this->isModify()) {
            $rate = $this->get('rate') ? $this->get('rate') : $this->getRate();
            $summa->add($rate);
        }

        return $summa;
    }

    /**
     * Plugin even triggered when the payment plugin notifies for the transaction
     * @param array $params The data received
     * @return null|void
     */
    public function isValid($params = array())
    {
        return false;
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
     * @param      $name
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
        $oldStatus = (string)$this->getStatus();
        $newStatus = (string)$newStatus;

        $isChanged = $oldStatus // is not first set on order creating
            && $oldStatus != JBCartStatusHelper::UNDEFINED // old is not empty
            && $newStatus != JBCartStatusHelper::UNDEFINED // new is not empty
            && $oldStatus != $newStatus; // is really changed

        $this->set('status', $newStatus);

        if ($isChanged) {
            $this->app->jbevent->fire($this->getOrder(), 'basket:paymentStatus', array(
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
            ));
        }
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
     * Default payment currency
     * @return mixed
     */
    public function getDefaultCurrency()
    {
        return $this->config->get('currency', 'eur');
    }

    /**
     * @param array $params
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
     * @return JBCartValue
     */
    public function getOrderSumm()
    {
        $order = $this->getOrder();
        return $order->getTotalSum();
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
     * @return JBCartValue
     */
    public function getRequestOrderSum()
    {
        return -1;
    }

    /**
     * @return string
     */
    public function getOrderDescription()
    {
        return 'Order #' . $this->getOrderId() . ' from ' . JUri::getInstance()->getHost();
    }

    /**
     * @return int
     */
    public function isModify()
    {
        return (int)$this->config->get('modifytotal', 0);
    }

    /**
     * Render shipping in order
     * @param  array
     * @return bool|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'value'  => $this->get('value'),
                'fields' => $this->get('fields', array()),
            ));
        }
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        $curStatus  = $this->getStatus()->getCode();
        $paidStatus = JBCart::getInstance()->getPaymentSuccess();

        return
            ($curStatus && $paidStatus) &&
            ($paidStatus != JBCartStatusHelper::UNDEFINED) &&
            ($curStatus == $paidStatus);
    }
}

/**
 * Class JBCartElementPaymentException
 */
class JBCartElementPaymentException extends JBCartElementException
{
}
