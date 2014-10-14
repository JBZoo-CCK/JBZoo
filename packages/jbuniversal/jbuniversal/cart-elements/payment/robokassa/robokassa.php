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
 * Class JBCartElementPaymentRobokassa
 */
class JBCartElementPaymentRobokassa extends JBCartElementPayment
{
    /**
     * @var string
     */
    private $_realUrl = 'https://merchant.roboxchange.com/Index.aspx';

    /**
     * @var string
     */
    private $_testUrl = 'http://test.robokassa.ru/Index.aspx';

    /**
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $merchantUrl = $this->isDebug() ? $this->_testUrl : $this->_realUrl;

        $fields = array(
            'OutSum'         => $this->getOrderSumm(),
            'InvId'          => $this->getOrderId(),
            'MrchLogin'      => $this->config->get('login'),
            'Desc'           => $this->getOrderDescription(),
            'SignatureValue' => $this->_getSignature(),
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
        return md5(implode(':', array(
            $this->config->get('login'),
            $this->getOrderSumm(),
            $this->getOrderId(),
            $this->config->get('password1'),
        )));
    }

    /**
     * @return int
     */
    public function getRequestOrderId()
    {
        return $this->app->jbrequest->get('InvId');
    }

    /**
     * Detect order id from merchant's robot request
     * @return int
     */
    public function getRequestOrderSum()
    {
        return $this->_jbrequest->get('OutSum');
    }

}
