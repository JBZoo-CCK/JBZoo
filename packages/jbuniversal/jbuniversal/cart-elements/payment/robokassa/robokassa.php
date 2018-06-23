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
 * Class JBCartElementPaymentRobokassa
 */
class JBCartElementPaymentRobokassa extends JBCartElementPayment
{

    /**
     * @var string
     */
    private $_realUrl = 'https://auth.robokassa.ru/Merchant/Index.aspx';

    /**
     * @var string
     */
    private $_testUrl = 'https://auth.robokassa.ru/Merchant/Index.aspx';

    /**
     * Redirect to payment action
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $orderAmount = $this->_getOrderAmount();
        $merchantUrl = $this->isDebug() ? $this->_testUrl : $this->_realUrl;

        $fields = array(
            'OutSum'         => $orderAmount->val(),
            'InvId'          => $this->getOrderId(),
            'MrchLogin'      => $this->config->get('login'),
            'Desc'           => $this->getOrderDescription(),
            'SignatureValue' => $this->_getSignature(),
        );

        if ($this->isDebug()) {
            $fields['IsTest'] = 1;
        }

        return $merchantUrl . '?' . $this->_jbrouter->query($fields);
    }

    /**
     * Checks validation
     * @return null|void
     * @throws AppException
     */
    public function isValid($params = array())
    {
        $crc   = JString::trim(JString::strtoupper($_REQUEST["SignatureValue"]));
        $myCrc = JString::trim(JString::strtoupper(md5(implode(':', array(
            $_REQUEST['OutSum'],
            $this->getOrderId(),
            $this->config->get('password2')
        )))));

        if ($crc === $myCrc) {
            return true;
        }

        return false;
    }

    /**
     * Get security signature
     * @return string
     */
    protected function _getSignature()
    {
        $orderAmount = $this->_getOrderAmount();

        return md5(implode(':', array(
            $this->config->get('login'),
            $orderAmount->val(),
            $this->getOrderId(),
            $this->config->get('password1'),
        )));
    }

    /**
     * Detect order id from merchant's robot request
     * @return int
     */
    public function getRequestOrderId()
    {
        return $this->app->jbrequest->get('InvId');
    }

    /**
     * Detect order id from merchant's robot request
     * @return int|JBCartValue
     */
    public function getRequestOrderSum()
    {
        return $this->_getOrderAmount();
    }

    /**
     * Get order amount
     * @return $this
     */
    protected function _getOrderAmount()
    {
        $order       = $this->getOrder();
        $payCurrency = $this->getDefaultCurrency();

        return $this->_order->val($this->getOrderSumm(), $order->getCurrency())->convert($payCurrency);
    }

}
