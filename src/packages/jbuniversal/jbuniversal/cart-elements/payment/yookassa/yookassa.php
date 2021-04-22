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
 * Class JBCartElementPaymentYooKassa
 */
class JBCartElementPaymentYooKassa extends JBCartElementPayment
{   
    const CACHE_TTL = 1440;

    /**
     * string
     */
    protected $_apiUrl = 'https://api.yookassa.ru/v3/payments';

    /**
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_session  = JFactory::getSession();

        JFactory::getLanguage()->load('com_jbzoo_cart_elements_payment_yookassa', $this->app->path->path('jbapp:cart-elements').'/payment/yookassa', null, true);
    }

    /**
     * Redirect to payment action
     * @return null|string
     */
    public function getRedirectUrl()
    {   
        $orderAmount    = $this->_getOrderAmount();
        $orderId        = $this->_order->id;

        $sessionUrl     = $this->_session->get('yookassa_redirect_url_order_'.$orderId);

        if ($sessionUrl) {
            return $sessionUrl;
        }

        $fields = array(
            'amount'            => array(
                'value'     => $orderAmount->val(),
                'currency'  => JString::strtoupper($this->getDefaultCurrency())
            ),
            'receipt'       => $this->getMerchantdData(),
            'description'   => $this->getOrderDescription(),
            'confirmation'  => array(
                'type'          => 'redirect',
                'return_url'    => $this->_jbrouter->payment('wait').'&yooKassaOrderId='.$orderId
            ),
            'test'          => $this->isDebug(),
            'capture'       => true,
            'metadata'      => array(
                'orderId'   => $orderId,
                'sign'      => $this->getSignature()
            )
        );

        $data = json_encode($fields);

        $paymentObj = $this->createOrder($data);

        if ($paymentObj && $redirectUrl = $paymentObj['confirmation']['confirmation_url']) {
            $this->_session->set('yookassa_redirect_url_order_'.$orderId, $redirectUrl);

            // Set YooMoney order id

            $this->set('order_id', $paymentObj['id']);
            $this->set('summary', json_encode($paymentObj));

            // Save order

            JBModelOrder::model()->save($this->_order);
                       
            return $paymentObj['confirmation']['confirmation_url'];
        }

        return;
    }

    /**
     * Checking the MD5 sign
     * @param array $params
     * @return bool
     */
    public function isValid($params = array())
    {
        $input          = JFactory::getApplication()->input->json;
        $paymentObject  = $input->get('object', array(), 'array');
        $event          = $input->get('event', '', 'string');
        $orderAmount    = $this->_order->val($paymentObject['amount']['value'], $this->getDefaultCurrency());
        $orderId        = $paymentObject['metadata']['orderId'];
        $requestMD5     = $paymentObject['metadata']['sign'];

        $currentMD5 = md5(implode(':', array(
            $this->config->get('shop_id'),
            $orderAmount->val(),
            $orderId,
            $this->config->get('secret'),
        )));

        // Check MD5

        if ($requestMD5 == $currentMD5 && $event == 'payment.succeeded') {
            return true;
        }

        if ($requestMD5 == $currentMD5 && $event == 'payment.canceled') {
            // Set status
            $this->setFail();

            // Trigger event
            $this->app->jbevent->fire($this->order, 'basket:paymentFail', array());
        }

        $this->renderResponse();
    }

    /**
     * Detect order id from merchant's robot request
     * @return int
     */
    public function getRequestOrderId()
    {   
        $task = $this->_jbrequest->get('task');

        if ($task == 'paymentCallback') {
            $input          = JFactory::getApplication()->input->json;
            $paymentObject  = $input->get('object', array(), 'array');
            $orderId        = $paymentObject['metadata']['orderId'];

            return $orderId;
        }
        
        return $this->_jbrequest->get('yooKassaOrderId');
    }

    /**
     * @return JBCartValue
     */
    public function getRequestOrderSum()
    {   
        $input          = JFactory::getApplication()->input->json;
        $paymentObject  = $input->get('object', array(), 'array');
        $sum            = $paymentObject['amount']['value'];

        return $this->_order->val($sum, $this->getDefaultCurrency());
    }

    /**
     * Get order amount
     * @return JBCartValue
     */
    protected function _getOrderAmount()
    {
        $payCurrency = $this->getDefaultCurrency();

        return $this->_order->val($this->getOrderSumm(), $this->_order->getCurrency())->convert($payCurrency);
    }

    /**
     * {@inheritdoc}
     */
    public function renderResponse()
    {
        header("HTTP/1.1 200 OK");
        jexit();
    }

    /**
     * Get merchant data
     * @return array
     */
     public function getMerchantdData()
    {   
        $result         = array();
        $contact        = $this->config->get('contact', 'email');
        $contactEl      = $this->config->get($contact);
        $items          = array();

        // Items

        $orderItems     = (array) $this->_order->getItems();

        foreach ($orderItems as $item) {
            $modifiers = $this->_order->getModifiersOrderPrice();
            $itemPrice = JBCart::val($item['total']);

            if (!empty($modifiers)) {
                foreach ($modifiers as $modifier) {
                    $rate       = $modifier->getRate();
                    $itemPrice  = $itemPrice->add($rate);
                }
            }

            $items[] = array(
                'description'       => $item['item_name'],
                'quantity'          => $item['quantity'],
                'amount'            => array(
                    'value'     => $itemPrice->val(),
                    'currency'  => JString::strtoupper($this->getDefaultCurrency())
                ),
                'vat_code'          => $this->config->get('tax', 1),
                'payment_subject'   => $this->config->get('payment_subject', 'commodity'),
                'payment_mode'      => $this->config->get('payment_method', 'full_prepayment')
            );
        }

        // Add shipping

        if ($shipping = $this->_order->getShipping()) {
            if ($shipping->isModify()) {
                $rate   = $shipping->getRate();
                $title  = $shipping->getName();

                if ($rate->val()) {
                    $items[] = array(
                        'description'       => $title,
                        'quantity'          => 1,
                        'amount'            => array(
                            'value'     => $rate->val(),
                            'currency'  => JString::strtoupper($this->getDefaultCurrency())
                        ),
                        'vat_code'          => $this->config->get('tax', 1),
                        'payment_subject'   => $this->config->get('payment_subject', 'commodity'),
                        'payment_mode'      => $this->config->get('payment_method', 'full_prepayment')
                    );
                }
            }
        }

        $result['items'] = $items;

        // Customer

        $result[$contact] = JString::trim($this->_order->getFieldElement($contactEl)->data()['value']);

        return $result;
    }

    /**
     * @param $options
     * @return null|array
     */
    public function createOrder($options)
    {   
        $authotization  = base64_encode($this->config->get('shop_id').':'.$this->config->get('secret'));

        $response       = $this->app->jbhttp->request($this->_apiUrl, $options, array(
            'headers'   => array(
                'Content-Type'      => 'application/json',
                'Idempotence-Key'   => $this->getIdempotenceKey(),
                'Authorization'     => 'Basic '.$authotization,
            ),
            'cache'     => 0,
            'cache_ttl' => self::CACHE_TTL,
            'debug'     => 1,
            'method'    => 'post',
            'response'  => 'full'
        ));

        $result = json_decode($response->body, true);

        // Logging

        if ($this->isLog()) {
            $this->app->jblog->log('jbzoo.cart-elements.payment.yookassa', 'createOrder', $result);
        }

        if (!isset($result['type']) && $result['type'] != 'error') {
            return $result;
        }

        return null;
    }

    /**
     * @param $options
     * @return null|array
     */
    public function getOrderInfo($orderId)
    {   
        $authotization  = base64_encode($this->config->get('shop_id').':'.$this->config->get('secret'));

        $response       = $this->app->jbhttp->request($this->_apiUrl.'/'.$orderId, array(), array(
            'headers'   => array(
                'Authorization'     => 'Basic '.$authotization,
            ),
            'cache'     => 0,
            'cache_ttl' => self::CACHE_TTL,
            'debug'     => 1,
            'method'    => 'get',
            'response'  => 'full'
        ));

        $result = json_decode($response->body, true);

        // Logging

        if ($this->isLog()) {
            $this->app->jblog->log('jbzoo.cart-elements.payment.yookassa', 'getOrderInfo', $result);
        }

        if (!isset($result['type']) && $result['type'] != 'error') {
            return $result;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getIdempotenceKey()
    {
        $hexData  = bin2hex($this->bytes(16));
        $parts    = str_split($hexData, 4);
        $parts[3] = '4' . substr($parts[3], 1);
        $parts[4] = '8' . substr($parts[4], 1);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s',
            $parts
        );
    }

    /**
     * Return random bytes number
     * @param int $length
     * @return string
     */
    public function bytes($length)
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $chr = chr($this->int(0, 255));
            $result .= $chr;
        }

        return $result;
    }

    /**
     * Return random int
     * @param int|null
     * @param int|null
     * @return int
     */
    public static function int($min = null, $max = null)
    {
        if ($min === null) {
            $min = 0;
        }

        if ($max === null) {
            $max = PHP_INT_MAX;
        }

        return mt_rand($min, $max);
    }

    /**
     * Get security signature
     * @return string
     */
    protected function getSignature()
    {
        $orderAmount = $this->_getOrderAmount();

        return md5(implode(':', array(
            $this->config->get('shop_id'),
            $orderAmount->val(),
            $this->getOrderId(),
            $this->config->get('secret'),
        )));
    }

    /**
     * Check current state order from system
     * @return bool | null
     */
    public function getStatusUrl()
    {
        $orderId = $this->get('order_id');

        if ($orderId) {
            $orderInfo  = $this->getOrderInfo($orderId);

            $this->set('summary', json_encode($orderInfo));

            // Save order

            JBModelOrder::model()->save($this->_order);

            // Redirect
            
            if ($orderInfo['status'] == 'succeeded') {
                return $this->_jbrouter->payment('success').'&yooKassaOrderId='.$this->_order->id;
            } elseif ($orderInfo['status'] == 'canceled') {
                return $this->_jbrouter->payment('fail').'&yooKassaOrderId='.$this->_order->id;
            }
        }

        return;
    }
}