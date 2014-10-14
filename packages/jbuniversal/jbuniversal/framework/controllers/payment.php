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
 * Class paymentJBUniversalController
 */
class paymentJBUniversalController extends JBUniversalController
{
    /**
     * @var JBCartOrder
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
            $this->_error('Order id not found');
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
        $this->app->jbdoc->rawOutput();

        $cart    = JBCart::getInstance();
        $payment = $this->order->getPayment();

        // check payment element
        if (empty($payment)) {
            $this->_error('Order #' . $this->order->id . ': was saved without payment element');
        }

        // payment is exists
        if (!$this->_orderInfo->get('type')) {
            $this->_error('Order #' . $this->order->id . ': Undefined payment system');
        }

        // check payment type
        if ($payment->getType() != $this->_orderInfo->get('type')) {
            $this->_error('Order #' . $this->order->id . ': Payment type is not correct');
        }

        // current status is not complited
        if ($payment->getStatus() == $cart->getPaymentSuccess()) {
            $this->_error('Order #' . $this->order->id . ': Status is success already');
        }

        // check summ
        $realSum    = $this->_jbmoney->clearValue($payment->getOrderSumm());
        $requestSum = $this->_jbmoney->clearValue($payment->getRequestOrderSum());
        if ($realSum != $requestSum) {
            $this->_error('Order #' . $this->order->id . ': Not correct amount');
        }

        // check if sum was empty
        if ($realSum <= 0) {
            $this->_error('Order #' . $this->order->id . ': Amount less or equal zero');
        }

        // checking of payment element
        if ($payment->isValid()) {
            $payment->setSuccess();
            $payment->renderResponse();
        } else {
            $this->_error('Order #' . $this->order->id . ': No valid request');
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
        //jbdump::log($message);
        throw new AppException($message);
    }

}

