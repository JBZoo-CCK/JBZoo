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
 * Class JBCartElementPaymentLiqPay
 */
class JBCartElementPaymentLiqPay extends JBCartElementPayment
{

    const VERSION = 3;

    /**
     * Payment uri
     * @var string
     */
    private $_uri = 'https://www.liqpay.com/api/checkout';

    /**
     * Redirect to payment form action
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $order      = $this->getOrder();
        $orderId    = $this->getOrderId();
        $publicKey  = JString::trim($this->config->get('public_key'));
        $privateKey = JString::trim($this->config->get('private_key'));

        $payCurrency = $this->getDefaultCurrency();
        $orderAmount = $this->_order->val($this->getOrderSumm(), $order->getCurrency())->convert($payCurrency);

        $data = array(
            'version'     => self::VERSION,
            'amount'      => $orderAmount->val(),
            'currency'    => JString::strtoupper($payCurrency),
            'public_key'  => $publicKey,
            'description' => $this->getOrderDescription(),
            'order_id'    => $orderId,
            'server_url'  => $this->_jbrouter->payment('callback'),
            'result_url'  => $this->_jbrouter->payment('success') . '&orderId=' . $this->getOrderId()
        );

        if ($this->isDebug()) {
            $data = array_merge($data, array('sandbox' => 1));
        }

        $dataEncode = base64_encode(json_encode($data));
        $signature  = base64_encode(sha1($privateKey . $dataEncode . $privateKey, 1));

        return $this->_uri . '?' . $this->_jbrouter->query(array(
            'data'      => $dataEncode,
            'signature' => $signature
        ));
    }

    /**
     * Checks validation
     * @param array $params
     * @return bool|null|void
     */
    public function isValid($params = array())
    {
        $requestData = $this->app->jbrequest->get('data');
        $privateKey  = JString::trim($this->config->get('private_key'));

        $signature   = base64_encode(sha1($privateKey . $requestData . $privateKey, 1));
        $requestSign = $this->app->jbrequest->get('signature');
        $data        = $this->_decodeData($requestData);

        if ($signature === $requestSign && in_array($data->status, array('success', 'sandbox')))
        {
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
            return $this->app->jbrequest->get('orderId');
        }

        if ($data = $this->app->jbrequest->get('data')) {
            $data = $this->_decodeData($data);
            return $data->order_id;
        }
    }

    /**
     * Detect order id from merchant's robot request
     * @return int|JBCartValue
     */
    public function getRequestOrderSum()
    {
        $order  = $this->getOrder();
        $data   = $this->_decodeData($this->app->jbrequest->get('data'));
        $amount = $this->_order->val($data->amount, $order->getCurrency());

        return $amount;
    }

    /**
     * Decode liq pay request data
     * @param $data
     * @return mixed
     */
    protected function _decodeData($data)
    {
        return json_decode(base64_decode($data));
    }

}
