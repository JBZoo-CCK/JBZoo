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
 * Class JBCartElementPaymentInterkassa
 */
class JBCartElementPaymentInterkassa extends JBCartElementPayment
{

    const HASH_MD5    = 'md5';
    const HASH_SHA256 = 'sha256';

    /**
     * Redirect to payment action
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $order       = $this->getOrder();
        $merchantUrl = 'https://sci.interkassa.com/';
        $payCurrency = $this->getDefaultCurrency();
        $orderAmount = $this->_order->val($this->getOrderSumm(), $order->getCurrency())->convert($payCurrency);

        $fields      = array(
            'ik_co_id' => $this->config->get('shopid'),
            'ik_pm_no' => $this->getOrderId(),
            'ik_am'    => $orderAmount->val(),
            'ik_cur'   => $payCurrency,
            'ik_ia_u'  => $this->_jbrouter->payment('callback'),
            'ik_ia_m'  => 'post',
            'ik_suc_u' => $this->_jbrouter->payment('success'),
            'ik_suc_m' => 'post',
            'ik_fal_u' => $this->_jbrouter->payment('fail'),
            'ik_fal_m' => 'post',
            'ik_pnd_u' => $this->_jbrouter->payment('success'),
            'ik_pnd_m' => 'post',
            'ik_desc'  => $this->getOrderDescription(),
            'ik_enc'   => 'utf-8',
            'ik_int'   => 'web',
            'ik_am_t'  => 'invoice',
        );

        // add hash
        $fields['ik_sign'] = $this->_getIkHash($fields, $this->config->get('key'));

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
        $shopid    = JString::trim(JString::strtoupper($this->config->get('shopid')));
        $reqShopid = JString::trim(JString::strtoupper($this->app->jbrequest->get('ik_co_id')));
        if ($reqShopid !== $shopid) {
            throw new JBCartElementPaymentException('Not correct shopid');
        }

        $status = JString::trim(JString::strtoupper($this->app->jbrequest->get('ik_inv_st')));
        if ($status !== 'SUCCESS') {
            throw new JBCartElementPaymentException('Not correct status');
        }

        $isTest = $this->_checkIkHash($this->config->get('test_key'));
        if ($this->isDebug() && $isTest) {
            return true;
        }

        $isReal = $this->_checkIkHash($this->config->get('key'));
        if (!$this->isDebug() && $isReal) {
            return true;
        }

        return false;
    }

    /**
     * Check interkassa v2 Hash
     * @param $ikSecret
     * @return bool
     */
    protected function _checkIkHash($ikSecret)
    {
        $requestHash = $this->_getIkHash($_REQUEST, $ikSecret);
        return $requestHash === $_REQUEST['ik_sign'];
    }

    /**
     * Detect order id from merchant's robot request
     * @return int|void
     */
    public function getRequestOrderId()
    {
        return $this->app->jbrequest->get('ik_pm_no');
    }

    /**
     * Detect order id from merchant's robot request
     * @return int|JBCartValue
     */
    public function getRequestOrderSum()
    {
        $order  = $this->getOrder();
        $amount = $this->_order->val($this->_jbrequest->get('ik_am'), $order->getCurrency());

        return $amount;
    }

    /**
     * @param $data
     * @param $secretKey
     * @return string
     */
    private function _getIkHash($data, $secretKey)
    {
        // get only interkassa fields
        if (isset($data['ik_sign'])) {
            unset($data['ik_sign']);
        }

        $fields = array();
        foreach ($data as $key => $value) {
            if (preg_match('#^ik_#', $key)) {
                $fields[$key] = $value;
            }
        }

        // sort it
        ksort($fields, SORT_STRING);

        // add secret key
        $secretKey = JString::trim($secretKey);
        array_push($fields, $secretKey);

        // get hash
        $hash   = '';
        $string = implode(':', $fields);
        $method = $this->config->get('hash_method', self::HASH_MD5);
        if (self::HASH_MD5 == $method) {
            $hash = md5($string, true);

        } else if (self::HASH_SHA256 == $method) {
            $hash = hash('sha256', $string, true);
        }

        $base64Hash = base64_encode($hash);

        return $base64Hash;
    }

}
