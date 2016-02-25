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
 * Class paymentJBUniversalController
 */
class PaymentJBUniversalController extends JBUniversalController
{
    /**
     * @var JBCartOrder|null
     */
    public $order = null;

    /**
     * @var JBModelOrder
     */
    protected $_orderModel = null;

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney;

    /**
     * @var JSONData
     */
    protected $_orderInfo;

    /**
     * @var AppTemplate
     */
    protected $template;

    /**
     * Init controller
     */
    protected function _init()
    {
        $this->app->jbdoc->noindex();

        $this->_orderModel = JBModelOrder::model();
        $this->_jbmoney    = $this->app->jbmoney;

        $this->_orderInfo = $this->app->jbcartpayment->getInfoByRequest();
        $orderId          = $this->_orderInfo->get('id');

        if ($orderId > 0) {
            $this->order = $this->_orderModel->getById($orderId);
        } else {
            $this->_error('Variable name with order ID was not found');
        }

        if (empty($this->order)) {
            $this->_error('Order #' . $orderId . ' not found');
        }

        $application    = $this->app->zoo->getApplication();
        $this->template = $application->getTemplate();
    }

    /**
     * Action for robot from payment system
     * Validate and check order as success
     * @throws AppException
     */
    public function paymentCallback()
    {
        $this->_init();

        $this->app->jbevent->fire($this->order, 'basket:paymentCallback');
        $this->app->jbdoc->rawOutput();

        $cart = JBCart::getInstance();

        /** @type JBCartElementPayment $payment */
        $payment = $this->order->getPayment();

        // check payment element
        if (empty($payment)) {
            $this->_error('Saved without payment element');
        }

        // payment is exists
        if (!$this->_orderInfo->get('type')) {
            $this->_error('Undefined payment system');
        }

        // check payment type
        if ($payment->getType() != $this->_orderInfo->get('type')) {
            $this->_error('Payment type is not correct');
        }

        // current status is not completed
        if ($payment->getStatus() == $cart->getPaymentSuccess()) {
            $this->_error('Payment status is "' . $payment->getStatus()->getCode() . '" already');
        }

        // check sum
        $realSum    = $payment->getOrderSumm();
        $requestSum = $payment->getRequestOrderSum();

        if ($realSum->compare($requestSum, '!=', 2)) {
            $this->_error('Not correct amount: ' . $realSum->data(true) . ' != ' . $requestSum->data(true));
        }

        // check if sum was empty
        if ($realSum->compare(0, '<=')) {
            $this->_error('Amount less or equal zero: ' . $realSum->data(true));
        }

        // checking of payment element
        if ($payment->isValid()) {

            $payment->setSuccess();
            $this->app->event->dispatcher->notify($this->app->event->create($this->order, 'basket:paymentSuccess'));
            $payment->renderResponse();

        } else {
            $this->_error('No valid request');
        }
    }

    /**
     * Payment success page
     */
    public function paymentSuccess()
    {
        $this->_init();
        $this->getview('payment_success')->addtemplatepath($this->template->getpath())->setlayout('payment_success')->display();
    }

    /**
     * Payment fail page
     */
    public function paymentFail()
    {
        $this->_init();

        JFactory::getSession()->set('application.queue', null); // HACK remove success order's messages

        $this->getview('payment_fail')->addtemplatepath($this->template->getpath())->setlayout('payment_fail')->display();
    }

    /**
     * @param $message
     * @throws AppException
     */
    protected function _error($message)
    {
        if ($this->order) {
            $message = 'Order #' . $this->order->id . ': ' . $message;
        } else {
            $message = 'Undefined Order: ' . $message;
        }

        /** @var JBDebugHelper $debuger */
        $debuger = $this->app->jbdebug;

        $debuger->log($message);
        $debuger->logArray($_POST, '_POST');
        $debuger->logArray($_GET, '_GET');
        $debuger->logArray($_REQUEST, '_REQUEST');

        $this->app->jbevent->fire($this->order, 'basket:paymentFail', array(
            'message' => $message,
        ));

        if (!JDEBUG) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            jexit('500 Internal Server Error');
        }

        jexit($message);
    }

}

