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
 * Class JBCartElementPaymentQiwi
 */
class JBCartElementPaymentQiwi extends JBCartElementPayment
{

    /**
     * @var string
     */
    private $_uri = 'https://w.qiwi.com/order/external/main.action';

    /**
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
     * @param array $params
     * @return bool|null|void
     */
    public function isValid($params = array())
    {
        $signPsw = JString::trim($this->config->get('sign_psw'));

        $hashData = $this->_bindRequestData(array(
            $this->app->jbrequest->get('amount'),
            $this->app->jbrequest->get('bill_id'),
            $this->app->jbrequest->get('ccy'),
            $this->app->jbrequest->get('command'),
            $this->app->jbrequest->get('comment'),
            $this->app->jbrequest->get('error'),
            $this->app->jbrequest->get('prv_name'),
            $this->app->jbrequest->get('status'),
            $this->app->jbrequest->get('user'),
        ));

        $billHashSign   = base64_encode(hash_hmac('sha1', $hashData, $signPsw, true));
        $requestHeaders = apache_request_headers();

        if ($requestHeaders['X-Api-Signature'] === $billHashSign) {
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

        if ($task == 'paymentSuccess' || $task == 'paymentFail') {
            return $this->app->jbrequest->get('order');
        }

        return $this->app->jbrequest->get('bill_id');
    }

    /**
     * Detect order id from merchant's robot request
     * @return int
     */
    public function getRequestOrderSum()
    {
        return $this->app->jbrequest->get('amount');
    }

    /**
     * Render Qiwi response
     * @return void
     */
    public function renderResponse()
    {
        header("HTTP/1.1 200 OK");
        header('content-type: text/xml; charset=UTF-8');
        $response = '<!--?xml version="1.0"?--><result><result_code>0</result_code></result>';
        jexit($response);
    }

    /**
     * Render payment form
     * @return null
     */
    public function renderPaymentForm()
    {
        return $this->app->jbform->render('default', array(
            'action' => 'index.php?' . $this->_jbrouter->query(array(
                    'option'     => 'com_zoo',
                    'controller' => 'basket',
                    'task'       => 'form',
                    'orderId'    => $this->_jbrequest->get('orderId')
                )),
            'submit' => JText::_('JBZOO_BUTTON_SUBMIT_SHOW')
        ));
    }

    /**
     * Payment form action
     * @return null|string
     */
    public function actionPaymentForm()
    {
        $order   = $this->getOrder();
        $billId  = $this->getOrderId();
        $restPw  = JString::trim($this->config->get('psw'));
        $shopId  = JString::trim($this->config->get('shop_id'));
        $restId  = JString::trim($this->config->get('rest_id'));
        $request = $this->_jbrequest->get('jbzooform');

        $lifeTime    = (int)$this->config->get('lifetime', 10);
        $lifeTimeISO = JHtml::date(time() + $lifeTime * 60, 'c');
        $shopConfig  = JBModelConfig::model()->getGroup('cart.config');

        $curlData = array(
            'amount'     => $this->getOrderSumm(),
            'pay_source' => 'qw',
            'lifetime'   => $lifeTimeISO,
            'ccy'        => $order->getCurrency(),
            'user'       => 'tel:' . JString::trim($request['phone']),
            'comment'    => $this->getOrderDescription(),
            'prv_name'   => $shopConfig->get('shop_name')
        );

        $httpAuthHeaderKey = base64_encode($restId . ':' . $restPw);
        $httpResponse      = $this->_createBill($shopId, $billId, $curlData, $httpAuthHeaderKey);
        $response          = json_decode($httpResponse->body);

        if ($response->response->result_code === 0) {

            $query = array(
                'shop'        => $shopId,
                'transaction' => $billId,
                'failUrl'     => $this->_jbrouter->payment('fail'),
                'successUrl'  => $this->_jbrouter->payment('success')
            );

            return $this->_uri . '?' . $this->_jbrouter->query($query);
        }

        $this->app->jbnotify->warning(JText::_($response->response->description));

        return null;
    }

    /**
     * Validate payment form
     * @param array $request
     * @return array|bool
     */
    public function validatePaymentForm($request = array())
    {
        $check = $request['jbzooform'];

        $value = $this->app->validator
            ->create('string', array('required' => true), array('required' => JText::_('Please enter the phone number.')))
            ->clean($check['phone']);

        return compact('value');
    }

    /**
     * Create qiwi bill
     * @param null $shopId
     * @param $billId
     * @param array $data
     * @param $authHeaderKey
     * @return JHttpResponse
     */
    protected function _createBill($shopId = null, $billId, $data = array(), $authHeaderKey)
    {
        $httpClient = JHttpFactory::getHttp();
        $qiwiCurl   = $this->_getQiwiCurl($shopId, $billId);

        return $httpClient->put($qiwiCurl, $this->_jbrouter->query($data), array(
            'Accept'        => 'application/json',
            'Authorization' => 'Basic ' . $authHeaderKey
        ));
    }

    /**
     * Get data for qiwi bill
     * @param $shopId
     * @param $billId
     * @param $restId
     * @param $restPw
     * @return mixed
     */
    protected function _getBillData($shopId, $billId, $restId, $restPw)
    {
        $httpClient = JHttpFactory::getHttp();
        $qiwiCurl   = $this->_getQiwiCurl($shopId, $billId);

        $httpResponse = $httpClient->get($qiwiCurl, array(
            'Accept'         => 'application/json',
            'Request Method' => 'GET',
            'Authorization'  => 'Basic ' . base64_encode($restId . ':' . $restPw)
        ));

        return $httpResponse;
    }

    /**
     * Get qiwi curl
     * @param $shopId
     * @param $billId
     * @return string
     */
    protected function _getQiwiCurl($shopId, $billId)
    {
        return 'https://w.qiwi.com/api/v2/prv/' . $shopId . '/bills/' . $billId;
    }

    /**
     * Bind request data
     * @param array $request
     * @return string
     */
    protected function _bindRequestData($request = array())
    {
        return implode('|', $request);
    }

}