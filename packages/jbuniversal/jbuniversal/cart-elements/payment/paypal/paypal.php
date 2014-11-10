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
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $merchantUrl = $this->isDebug() ? $this->_testUrl : $this->_realUrl;

        $order  = $this->getOrder();
        $defCur = $this->_jbmoney->getDefaultCur();
        $summ   = $this->_jbmoney->convert($defCur, $defCur, $order->getTotalSum(false));

        $fields = array(
            'cmd'           => '_xclick',
            'no_shipping'   => '1',
            'rm'            => '2',
            'business'      => JString::trim($this->config->get('email')),
            'item_number'   => $order->id,
            'amount'        => $summ,
            'currency_code' => $order->getTotalSum(false),
            'return'        => $this->_jbrouter->payment('success'),
            'cancel_return' => $this->_jbrouter->payment('fail'),
            'notify_url'    => $this->_jbrouter->payment('callback'),
            'item_name'     => $this->getOrderDescription(),
        );

        return $merchantUrl . '?' . $this->_jbrouter->query($fields);
    }

    /**
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
        $response   = JHttpFactory::getHttp()->post($merchantUrl, $checkParam);

        if (JString::strtoupper(JString::trim($response->body)) == 'VERIFIED') {
            return true;
        }

        return false;
    }

    /**
     * @return int|void
     */
    public function getRequestOrderId()
    {
        return $this->app->jbrequest->get('item_number');
    }

    /**
     * @return float
     */
    public function getRequestOrderSum()
    {
        return $this->app->jbrequest->get('mc_gross');
    }


}
