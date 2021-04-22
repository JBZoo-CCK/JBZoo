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
 * Class JBCartElementPaymentSberbank
 */

class JBCartElementPaymentSberbank extends JBCartElementPayment
{   
    const CACHE_TTL = 1440;

    /**
     * @var string
     */
    private $_prodUrl = 'https://securepayments.sberbank.ru/payment/rest/';

    /**
     * @var string
     */
    private $_testUrl = 'https://3dsec.sberbank.ru/payment/rest/';

    /**
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_session  = JFactory::getSession();

        JFactory::getLanguage()->load('com_jbzoo_cart_elements_payment_sberbank', $this->app->path->path('jbapp:cart-elements').'/payment/sberbank', null, true);
    }

    /**
     * Redirect to payment action
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $order          = $this->getOrder();
        $payCurrency    = $this->getDefaultCurrency();
        $orderId        = $this->getOrderId();
        $orderAmount    = $this->_order->val($this->getOrderSumm(), $order->getCurrency())->convert($payCurrency);
        $orderAmount    = $orderAmount->val() * 100;

        $sessionUrl     = $this->_session->get('sberbank_redirect_url_order_'.$orderId);

        if ($sessionUrl) {
            return $sessionUrl;
        }
        
        $options = array(
            'userName'            => JString::trim($this->config->get('merchant')),
            'password'            => JString::trim($this->config->get('password')),
            'amount'              => $orderAmount,
            'orderNumber'         => $orderId,
            'dynamicCallbackUrl'  => $this->_jbrouter->payment('callback').'&sberbankOrderId='.$order->id,
            'returnUrl'           => $this->_jbrouter->payment('success').'&sberbankOrderId='.$order->id,
            'failUrl'             => $this->_jbrouter->payment('fail').'&sberbankOrderId='.$order->id,
            'orderBundle'         => $this->getMerchantdData()
        );

        $method = 'register.do';
    
        $response = $this->apiRequest($options, $method);

        if (isset($response['formUrl']) && !empty($response['formUrl'])) {
            $this->_session->set('sberbank_redirect_url_order_'.$orderId, $response['formUrl']);

            // Set sberbank order id

            $this->set('order_id', $response['orderId']);
            $this->set('summary', json_encode($response));

            // Save order

            JBModelOrder::model()->save($order);

            return $response['formUrl'];
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
        $orderId = $this->app->jbrequest->get('sberbankOrderId');

        $options = array(
            'userName'       => $this->config->get('merchant'),
            'password'       => $this->config->get('password'),
            'orderNumber'    => $orderId
        );

        $method     = 'getOrderStatusExtended.do';
        $response   = $this->apiRequest($options, $method);

        $orderStatus = $response['OrderStatus'];

        if ($orderStatus == '1' || $orderStatus == '2') {
            return true;
        } else if ($orderStatus == '3') {
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
        $task = $this->app->jbrequest->get('task');

        if ($task == 'paymentCallback') {
            return $this->app->jbrequest->get('orderNumber');
        }
        
        return $this->app->jbrequest->get('sberbankOrderId');
    }
    
    /**
     * @return JBCartValue
     */
    public function getRequestOrderSum()
    {   
        $orderId = $this->app->jbrequest->get('sberbankOrderId');

        if ($task == 'paymentCallback') {
            $orderId = $this->app->jbrequest->get('orderNumber');
        }

        if ($orderId) {

            $options = array(
                'userName'      => $this->config->get('merchant'),
                'password'      => $this->config->get('password'),
                'orderNumber'   => $orderId
            );
        
            $method     = 'getOrderStatusExtended.do';
            $response   = $this->apiRequest($options, $method);

            if (isset($response['amount'])) {
                $sum = $response['amount'];

                return $this->_order->val($sum/100, $this->getDefaultCurrency());
            }
        }

        return;
    }

    /**
     * Get merchant data
     * @return array
     */
    public function getMerchantdData()
    {   
        $result         = array();
        $customerData   = $this->getCustomerdData();
        $items          = array();

        $order          = $this->getOrder();
        $orderItems     = (array)$order->getItems();

        $i              = 0;

        foreach ($orderItems as $key => $item) {

            $modifiers = $order->getModifiersOrderPrice();
            $itemPrice = JBCart::val($item['total']);

            if (!empty($modifiers)) {
                foreach ($modifiers as $modifier) {
                    $rate       = $modifier->get('rate');
                    $itemPrice  = $itemPrice->add($rate);
                }
            }

            $items[] = array(
                'positionId'    => $i + 1,
                'name'          => $item['item_name'],
                'quantity'      => array(
                    'value'     => $item['quantity'],
                    'measure'   => JText::_('JBZOO_ELEMENT_PAYMENT_SBERBANK_ITEM_MEASURE')
                ),
                'itemPrice'     => $itemPrice->val() * 100,
                'itemCode'      => $i.'-'.$item['item_id'],
                'tax'           => array(
                    'taxType' => (int)$this->config->get('tax', 0),
                ),
                'itemAttributes' => array( // ФФД 1.05
                    "attributes" => array(
                        array(
                            "name"  => "paymentMethod",
                            "value" => (int)$this->config->get('payment_method', 1)
                        ),
                        array(
                            "name"  => "paymentObject",
                            "value" => (int)$this->config->get('payment_subject', 1)
                        ),
                    )
                )
            );

            $i++;
        }

        if ($shipping = $order->getShipping()) {
            if ($shipping->isModify()) {
                $rate   = $shipping->getRate();
                $title  = $shipping->getName();

                if ($rate->val()) {
                    $items[] = array(
                        'positionId'    => $i + 1,
                        'name'          => JText::_('JBZOO_ELEMENT_PAYMENT_SBERBANK_ITEM_SHIPPING').' '.$title,
                        'quantity'      => array(
                            'value'     => 1,
                            'measure'   => JText::_('JBZOO_ELEMENT_PAYMENT_SBERBANK_ITEM_MEASURE')
                        ),
                        'itemPrice'     => $rate->val() * 100,
                        'itemCode'      => $shipping->identifier,
                        'tax'           => array(
                            'taxType' => (int) $this->config->get('tax', 0),
                        ),
                        'itemAttributes' => array( // ФФД 1.05
                            "attributes" => array(
                                array(
                                    "name"  => "paymentMethod",
                                    "value" => 1
                                ),
                                array(
                                    "name"  => "paymentObject",
                                    "value" => 4
                                ),
                            )
                        )
                    );
                }
            }
        }

        $result['cartItems']        = array('items' => $items);

        if ($customerData) {
            $result['customerDetails'] = $customerData;
        }

        return json_encode($result);
    }  

    /**
     * Set customer data
     * @return array
     */
     public function getCustomerdData()
    {
        $result = array();

        $order = $this->getOrder();

        $contact        = $this->config->get('contact', 'email');
        $contactEl      = $this->config->get($contact);

        if (!empty($contactEl)) {
            $contactData = JString::trim($order->getFieldElement($contactEl)->data()['value']);

            if ($contactData) {
                $result[$contact] = $contactData;
            }
        }

        return $result;
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
     * @param $options
     * @param $method
     * @return null|array
     */
    public function apiRequest($options, $method)
    {   
        $url = $this->isDebug() ? $this->_testUrl : $this->_prodUrl;
        
        $response = $this->app->jbhttp->request($url.$method, $options, array(
            'headers'   => array('Content-Type' => 'application/x-www-form-urlencoded'),
            'cache'     => 0,
            'cache_ttl' => self::CACHE_TTL,
            'debug'     => 1,
            'method'    => 'post',
            'response'  => 'full'
        ));

        $result = json_decode($response->body, true);

        // Logging

        if ($this->isLog()) {
            $this->app->jblog->log('jbzoo.cart-elements.payment.sberbank', 'apiRequest:'.$method, $result);
        }

        return $result;
    }
}


