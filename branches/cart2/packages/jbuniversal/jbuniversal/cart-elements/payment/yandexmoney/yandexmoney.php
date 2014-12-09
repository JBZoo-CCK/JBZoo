<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Kalistratov Sergey <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementPaymentYandexMoney
 */
class JBCartElementPaymentYandexMoney extends JBCartElementPayment
{

    /**
     * Payment uri
     * @var string
     */
    private $_uri = 'https://money.yandex.ru/embed/shop.xml';

    /**
     * Redirect to payment form action
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $query = array(
            'option'     => 'com_zoo',
            'controller' => 'basket',
            'task'       => 'form',
            'orderId'    => $this->getOrderId()
        );

        return 'index.php?' . $this->_jbrouter->query($query);
    }

    /**
     * Render payment form
     * @return null
     */
    public function renderPaymentForm()
    {
        $order       = $this->getOrder();
        $targets     = $this->getOrderDescription();
        $payCurrency = $this->getDefaultCurrency();
        $account     = JString::trim($this->config->get('account_number'));
        $orderAmount = $this->_order->val($this->getOrderSumm(), $order->getCurrency())->convert($payCurrency);
        $successUrl  = $this->_jbrouter->payment('success') . '&bill_id=' . $this->getOrderId();

        $query = array(
            'account'     => $account,
            'quickpay'    => 'shop',
            'writer'      => 'seller',
            'targets'     => $targets,
            'default-sum' => $orderAmount->val(),
            'button-text' => '01',
            'label'       => $this->getOrderId(),
            'successURL'  => $successUrl,
            'payment-type-choice' => 'on'
        );

        $orderUri = $this->_uri . '?' . $this->_jbrouter->query($query);

        if ($layout = $this->getLayout('form.php')) {
            return self::renderLayout($layout, array(
                'orderUri' => $orderUri
            ));
        }
    }

    /**
     * Checks validation
     * @param array $params
     * @return bool|null|void
     */
    public function isValid($params = array())
    {
        $requestHash = $this->app->jbrequest->get('sha1_hash');

        $bindRequest = implode('&', array(
            $this->app->jbrequest->get('notification_type'),
            $this->app->jbrequest->get('operation_id'),
            $this->app->jbrequest->get('amount'),
            $this->app->jbrequest->get('currency'),
            $this->app->jbrequest->get('datetime'),
            $this->app->jbrequest->get('sender'),
            $this->app->jbrequest->get('codepro'),
            JString::trim($this->config->get('secret')),
            $this->app->jbrequest->get('label'),
        ));

        $hash = hash('sha1', $bindRequest);

        if ($hash === $requestHash) {
            return true;
        }

        return false;
    }

    /**
     * Detect order id from merchant's robot request
     * @return int
     */
    public function getRequestOrderId()
    {
        $task = $this->app->jbrequest->get('task');

        if ($task == 'paymentSuccess') {
            return $this->app->jbrequest->get('bill_id');
        }

        return $this->app->jbrequest->get('label');
    }

    /**
     * Render response for merchant
     * @return void
     */
    public function renderResponse()
    {
        jexit('OK');
    }

    /**
     * Set payment rate
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val($this->config->get('rate', '0.5%'));
    }

    /**
     * Detect order id from merchant's robot request
     * @return int|JBCartValue
     */
    public function getRequestOrderSum()
    {
        $order       = $this->getOrder();
        $orderAmount = $this->_order->val($this->app->jbrequest->get('withdraw_amount'), $order->getCurrency());

        return $orderAmount;
    }

}
