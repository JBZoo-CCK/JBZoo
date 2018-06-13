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
 * Class JBCartElementPaymentPayPal
 */
class JBCartElementPaymentPayPal extends JBCartElementPayment
{

    /**
     * @var string
     */
    private $_realUrl = 'https://www.paypal.com/cgi-bin/webscr';

    /**
     * @var string
     */
    private $_testUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

    /**
     * Redirect to payment action
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $order       = $this->getOrder();
        $payCurrency = $this->getDefaultCurrency();
        $merchantUrl = $this->isDebug() ? $this->_testUrl : $this->_realUrl;
        $orderAmount = $this->_order->val($this->getOrderSumm(), $order->getCurrency())->convert($payCurrency);

        $fields = array(
            'cmd'           => '_xclick',
            'no_shipping'   => 1,
            'rm'            => 2,
            'business'      => JString::trim($this->config->get('email')),
            'item_number'   => $order->id,
            'amount'        => $orderAmount->val(),
            'currency_code' => JString::strtoupper($payCurrency),
            'return'        => $this->_jbrouter->payment('success'),
            'cancel_return' => $this->_jbrouter->payment('fail'),
            'notify_url'    => $this->_jbrouter->payment('callback'),
            'item_name'     => $this->getOrderDescription(),
            'charset'       => 'utf-8',
        );

        return $merchantUrl . '?' . $this->_jbrouter->query($fields);
    }

    /**
     * Checks validation
     * @param array $params
     * @return null|void
     * @throws AppException
     */
    public function isValid($params = array())
    {
        // get debug mode
        $merchantUrl = $this->isDebug() ? $this->_testUrl : $this->_realUrl;

        // check via PayPal service
        $checkParam = array_merge(array('cmd' => '_notify-validate'), $_POST);
        $response   = $this->_requestCurl($merchantUrl, $checkParam);

        if ($this->app->jbvars->upper($response) == 'VERIFIED') {
            return true;
        }

        return false;
    }

    /**
     * Detect order id from merchant's robot request
     * @return int|void
     */
    public function getRequestOrderId()
    {
        return $this->app->jbrequest->get('item_number');
    }

    /**
     * Detect order id from merchant's robot request
     * @return int|JBCartValue
     */
    public function getRequestOrderSum()
    {
        $order  = $this->getOrder();
        $amount = $this->_order->val($this->_jbrequest->get('mc_gross'), $order->getCurrency());

        return $amount;
    }

    /**
     * Curl request
     * @param       $url
     * @param array $data
     * @throws AppException
     */
    protected function _requestCurl($url, $data = array())
    {
        if (function_exists('curl_init') && is_callable('curl_init')) {

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_jbrouter->query($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($curl);
            curl_close($curl);

            return $response;

        } else {
            throw new AppException('The module "curl" is not available.');
        }
    }

}
