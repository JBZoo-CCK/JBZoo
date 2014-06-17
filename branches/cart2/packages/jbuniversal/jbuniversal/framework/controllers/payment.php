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
 * Class paymentJBUniversalController
 */
class paymentJBUniversalController extends JBUniversalController
{

    const TYPE_PAYPAL   = 'PayPal';
    const TYPE_ROBOX    = 'Robokassa.ru';
    const TYPE_IKASSA   = 'Interkassa.com';
    const TYPE_IKASSA_2 = 'Interkassa.com V2';
    const TYPE_MANUAL   = 'Manual';

    /**
     * @var Int
     */
    public $appId = null;

    /**
     * @var Item
     */
    public $order = null;

    /**
     * @var Int
     */
    public $orderId = null;

    /**
     * @var Int
     */
    public $itemId = null;

    /**
     * @var ElementJBBasketItems
     */
    public $orderDetails = null;

    /**
     * @var AppTemplate
     */
    public $template = null;

    /**
     * @var ParameterData
     */
    public $appParams = null;

    /**
     * @var JBUniversalApplication
     */
    public $application = null;

    /**
     * @var JBModelOrder
     */
    public $orderModel = null;

    /**
     * @var BasketRenderer
     */
    public $renderer = null;

    /**
     * @var String
     */
    public $systemType = null;

    /**
     * Init controller
     * @throws AppException
     */
    protected function _init()
    {
        $this->app->jbdoc->noindex();

        $this->orderId = (int)$this->_jbrequest->get('order_id');
        $this->appId   = (int)$this->_jbrequest->get('app_id');

        $this->appParams = $this->application->getParams();

        if ($invId = (int)$this->_jbrequest->get('InvId')) {
            $this->systemType = self::TYPE_ROBOX;
            $this->orderId    = $invId;

        } else if ($ikPaymentId = (int)$this->_jbrequest->get('ik_payment_id')) {
            $this->systemType = self::TYPE_IKASSA;
            $this->orderId    = $ikPaymentId;

        } else if ($ikPaymentId = (int)$this->_jbrequest->get('ik_pm_no')) {
            $this->systemType = self::TYPE_IKASSA_2;
            $this->orderId    = $ikPaymentId;

        } else if ($paypalOrderId = (int)$this->_jbrequest->get('item_number')) {
            $this->systemType = self::TYPE_PAYPAL;
            $this->orderId    = $paypalOrderId;

        } else if ($orderId = (int)$this->_jbrequest->get('order_id')) {
            $this->systemType = self::TYPE_MANUAL;
            $this->orderId    = $orderId;
        }

        if (!$this->appId) {
            throw new AppException('Applciation id is no set');
        }

        if (!$this->template = $this->application->getTemplate()) {
            throw new AppException('No template selected');
        }

        if ((int)$this->appParams->get('global.jbzoo_cart_config.enable', 0) == 0) {
            throw new AppException('Application is not a basket');
        }

        if ((int)$this->appParams->get('global.jbzoo_cart_config.payment-enabled') == 0 && !isset($orderId)) {
            throw new AppException('Payment is not enabled');
        }

        if ($this->orderId) {

            $this->orderModel = JBModelOrder::model();
            if (!$this->order = $this->orderModel->getById($this->orderId)) {
                throw new AppException('Order #' . $this->orderId . ' no exists');
            }

            if (!$this->orderDetails = $this->orderModel->getDetails($this->order)) {
                throw new AppException('This type don\'t have JBPrice element');
            }
        }

        if (!$this->orderDetails) {
            //throw new AppException('Order not found');
        }

        // set renderer
        $this->renderer = $this->app->renderer->create('basket')->addPath(array(
            $this->app->path->path('component.site:'),
            $this->template->getPath()
        ));
    }

    /**
     * Index action
     */
    function index()
    {
        $this->_init();

        $totalSumm         = $this->orderDetails->getTotalPrice();
        $totalSummFormated = $this->orderDetails->getTotalPrice(true);

        $appParams      = $this->app->data->create($this->appParams->get('global.jbzoo_cart_config.', array()));
        $this->payments = array();

        if ($this->orderDetails->getOrderStatus() == ElementJBBasketItems::ORDER_STATUS_PAID) {
            throw new AppException('Order has already been paid');
        }
        if ($totalSumm == 0) {
            throw new AppException('To pay for the cost should be greater than zero');
        }

        // robox
        if ((int)$appParams->get('robox-enabled', 0)) {
            $params               = new stdClass();
            $params->login        = JString::trim($appParams->get('robox-login'));
            $params->password1    = JString::trim($appParams->get('robox-password1'));
            $params->hash         = md5(implode(':', array($params->login, $totalSumm, $this->orderId, $params->password1)));
            $params->summ         = $totalSumm;
            $params->orderId      = $this->orderId;
            $params->summFormated = $totalSummFormated;
            $params->debug        = (int)$appParams->get('robox-debug', 0);

            $this->payments['robox'] = $this->app->data->create($params);
        }

        // ikassa
        if ((int)$appParams->get('ikassa-enabled', 0)) {
            $params               = new stdClass();
            $params->shopid       = JString::trim($appParams->get('ikassa-shopid'));
            $params->key          = JString::trim($appParams->get('ikassa-key'));
            $params->currency     = JString::trim(strtoupper($appParams->get('currency', 'EUR')));
            $params->isNew        = (int)($appParams->get('ikassa-new'));
            $params->summ         = $totalSumm;
            $params->orderId      = $this->orderId;
            $params->summFormated = $totalSummFormated;
            $params->url_success  = $this->app->jbrouter->payment($this->appId, 'success');
            $params->url_fail     = $this->app->jbrouter->payment($this->appId, 'fail');
            $params->url_callback = $this->app->jbrouter->payment($this->appId, 'callback');

            $this->payments['ikassa'] = $this->app->data->create($params);
        }

        // paypal
        if ((int)$appParams->get('paypal-enabled', 0)) {
            $params               = new stdClass();
            $params->email        = JString::trim($appParams->get('paypal-email'));
            $params->debug        = (int)$appParams->get('paypal-debug', 0);
            $params->summ         = $totalSumm;
            $params->orderId      = $this->orderId;
            $params->summFormated = $totalSummFormated;
            $params->url_success  = $this->app->jbrouter->payment($this->appId, 'success');
            $params->url_fail     = $this->app->jbrouter->payment($this->appId, 'fail');
            $params->url_callback = $this->app->jbrouter->payment($this->appId, 'callback');
            $params->currency     = JString::trim(strtoupper($appParams->get('currency', 'USD')));

            $this->payments['paypal'] = $this->app->data->create($params);
        }

        // manual
        if ((int)$appParams->get('manual-enabled', 0)) {
            $params          = new stdClass();
            $params->title   = $appParams->get('manual-title');
            $params->text    = $appParams->get('manual-text');
            $params->message = $appParams->get('manual-message');

            $this->payments['manual'] = $this->app->data->create($params);
        }

        // display
        $this->getview('payment')->addTemplatePath($this->template->getPath())->setLayout('payment')->display();
    }

    /**
     * Action for robot from payment system
     * Validate and check order as success
     * @throws AppException
     */
    public function paymentCallback()
    {
        $this->_init();

        if ($this->orderDetails->getOrderStatus() == ElementJBBasketItems::ORDER_STATUS_PAID) {
            throw new AppException('Order has already been paid');
        }

        $totalsumm = $this->orderDetails->getTotalPrice();

        /////////////////////////////////////////// ROBOKASSA SYSTEM
        if ($this->systemType == self::TYPE_ROBOX) {

            if ((float)$totalsumm != (float)$_REQUEST['OutSum']) {
                throw new AppException('No valid summ');
            }

            $password2 = JString::trim($this->appParams->get('global.jbzoo_cart_config.robox-password2'));
            $crc       = strtoupper($_REQUEST["SignatureValue"]);
            $myCrc     = strtoupper(md5(implode(':', array($_REQUEST['OutSum'], $this->orderId, $password2))));

            if ($crc === $myCrc) {

                // get request vars
                $args = array(
                    'date'            => $this->app->date->create()->toSQL(),
                    'system'          => $this->systemType,
                    'additionalState' => null
                );

                // execute callback method
                $this->orderDetails->callback('paymentCallback', $args);

                jexit('OK' . $this->orderId);

            } else {
                throw new AppException('No valid hash');
            }

        } else if ($this->systemType == self::TYPE_IKASSA_2) {

            /////////////////////////////////////////// INTERKASSA V2 SYSTEM

            $appParams = $this->app->data->create($this->appParams->get('global.jbzoo_cart_config.', array()));

            $shopid    = trim(strtoupper($appParams->get('ikassa-shopid')));
            $reqShopid = trim(strtoupper($this->_jbrequest->get('ik_co_id')));
            if ($reqShopid !== $shopid) {
                throw new AppException('Not correct shopid');
            }

            $status = trim(strtoupper($this->_jbrequest->get('ik_inv_st')));
            if ($status !== 'SUCCESS') {
                throw new AppException('Not correct status');
            }

            $totalSumm     = $this->app->jbmoney->clearValue($this->orderDetails->getTotalPrice());
            $requestAmount = $this->app->jbmoney->clearValue($this->_jbrequest->get('ik_am'));
            if ($totalSumm !== $requestAmount) {
                throw new AppException('Not correct sum');
            }

            $real = $this->_checkIKv2Hash($_POST, $appParams->get('ikassa-key'));
            $test = $this->_checkIKv2Hash($_POST, $appParams->get('ikassa-key-test'));
            if ($real || $test) {

                $commentData = array(
                    'Invoice Id'      => $this->_jbrequest->get('ik_inv_id'),
                    'Payway Via'      => $this->_jbrequest->get('ik_pw_via'),
                    'Trans ID'        => $this->_jbrequest->get('ik_trn_id'),
                    'Create Date'     => $this->_jbrequest->get('ik_inv_crt'),
                    'Proc Date'       => $this->_jbrequest->get('ik_inv_prc'),
                    'Summa'           => $this->_jbrequest->get('ik_am'),
                    'Checkout Refund' => $this->_jbrequest->get('ik_co_rfn'),
                    'Paysystem Price' => $this->_jbrequest->get('ik_ps_price'),
                    'Currency'        => $this->_jbrequest->get('ik_cur'),
                );

                $success = 'Success';
                if ($test) {
                    $success = 'Success test';
                }

                $this->orderDetails->callback('paymentCallback', array(
                    'date'            => $this->app->date->create()->toSQL(),
                    'system'          => $this->systemType,
                    'additionalState' => $success,
                    'commet'          => $this->app->jbarray->toFormatedString($commentData),
                ));
            }

        } else if ($this->systemType == self::TYPE_IKASSA) {

            /////////////////////////////////////////// INTERKASSA SYSTEM

            $myCrcData = implode(':', array(
                $this->_jbrequest->get('ik_shop_id', ''),
                $this->_jbrequest->get('ik_payment_amount', ''),
                $this->_jbrequest->get('ik_payment_id', ''),
                $this->_jbrequest->get('ik_paysystem_alias', ''),
                $this->_jbrequest->get('ik_baggage_fields', ''),
                $this->_jbrequest->get('ik_payment_state', ''),
                $this->_jbrequest->get('ik_trans_id', ''),
                $this->_jbrequest->get('ik_currency_exch', ''),
                $this->_jbrequest->get('ik_fees_payer', ''),
                JString::trim($this->appParams->get('global.jbzoo_cart_config.ikassa-key'))
            ));

            $myCrc         = strtoupper(md5($myCrcData));
            $crc           = strtoupper($this->_jbrequest->get('ik_sign_hash'));
            $shopid        = $this->appParams->get('global.jbzoo_cart_config.ikassa-shopid');
            $requestShopid = $this->_jbrequest->get('ik_shop_id');
            $totalSumm     = (float)$this->orderDetails->getTotalPrice();
            $requestAmount = (float)$this->_jbrequest->get('ik_payment_amount');

            if ($crc === $myCrc &&
                $totalSumm == $requestAmount &&
                $requestShopid === $shopid
            ) {
                // get request vars
                $args = array(
                    'date'            => $this->app->date->create()->toSQL(),
                    'system'          => $this->systemType,
                    'additionalState' => $this->_jbrequest->get('ik_payment_state'),
                    //'commet'          => $this->app->jbarray->toFormatedString($commentData),                    
                );

                // execute callback method
                $this->orderDetails->callback('paymentCallback', $args);

                jexit('OK' . $this->orderId);

            } else {
                throw new AppException('No valid hash');
            }

        } else if ($this->systemType == self::TYPE_PAYPAL) {

            /////////////////////////////////////////// PAYPAL SYSTEM

            $appParams = $this->app->data->create($this->appParams->get('global.jbzoo_cart_config.', array()));
            $jbmoney   = $this->app->jbmoney;

            $totalSumm     = $jbmoney->clearValue($this->orderDetails->getTotalPrice());
            $requestAmount = $jbmoney->clearValue($this->_jbrequest->get('mc_gross'));

            // check summ
            if ($totalSumm && $requestAmount) {
                throw new AppException('Not correct sum');
            }

            // check currency
            $currency = strtoupper($this->_jbrequest->get('mc_currency'));
            $cartCur  = strtoupper($appParams->get('currency'));
            if ($currency != $cartCur) {
                throw new AppException('Not correct currency');
            }

            // check simple status
            $status = strtoupper($this->_jbrequest->get('payment_status'));
            if ($status != 'COMPLETED') {
                throw new AppException('Not correct status');
            }

            // get debug mode
            $checkUrl = 'https://www.paypal.com/cgi-bin/webscr';
            if ((int)$appParams->get('paypal-debug', 0)) {
                $checkUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            }

            // check via PayPal service
            $checkParam = array_merge(array('cmd' => '_notify-validate'), $_POST);
            $response   = JHttpFactory::getHttp()->post($checkUrl, $checkParam);
            if (strtoupper(trim($response->body)) != 'VERIFIED') {
                throw new AppException('No valid checked status');
            } else {

                // everything is OK!
                $commentData = array(
                    'Order ID'       => $this->orderId,
                    'Payment Status' => $this->_jbrequest->get('payment_status'),
                    'Pending Reason' => $this->_jbrequest->get('pending_reason'),
                    'Payment Type'   => $this->_jbrequest->get('payment_type'),
                    'Payer Email'    => $this->_jbrequest->get('payer_email'),
                    'Payer Name'     => $this->_jbrequest->get('first_name') . ' ' . $this->_jbrequest->get('last_name'),
                    'Payer Id'       => $this->_jbrequest->get('payer_id'),
                    'Payment Date'   => $this->_jbrequest->get('payment_date'),
                    'IPN Track Id'   => $this->_jbrequest->get('ipn_track_id'),
                    'Txn Id'         => $this->_jbrequest->get('txn_id'),
                    'Txn type'       => $this->_jbrequest->get('txn_type'),
                    'Currency'       => $this->_jbrequest->get('mc_currency'),
                );

                // get request vars && execute callback method
                $this->orderDetails->callback('paymentCallback', array(
                    'date'            => $this->app->date->create()->toSQL(),
                    'system'          => $this->systemType,
                    'additionalState' => $commentData['IPN Track Id'] . ' / ' . $commentData['Txn Id'],
                    'commet'          => $this->app->jbarray->toFormatedString($commentData),
                ));

                jexit('OK' . $this->orderId);
            }

        } else {
            throw new AppException('Unknown system');
        }
    }

    /**
     * Payment success page
     */
    public function paymentSuccess()
    {
        $this->_init();

        $appParams = $this->app->data->create($this->appParams->get('global.jbzoo_cart_config.', array()));

        // check custom success page
        $successPage = JString::trim($appParams->get('payment-page-success'));
        if (!empty($successPage)) {
            $successPage = $this->app->jbrouter->addParamsToUrl($successPage, array('order_id' => $this->order->id));
            $this->setRedirect($successPage);

            return;
        }

        // display
        $this->getview('payment_success')->addtemplatepath($this->template->getpath())->setlayout('payment_success')->display();
    }

    /**
     * Payment success page (manual)
     */
    public function paymentManual()
    {
        $this->_init();

        $appParams = $this->app->data->create($this->appParams->get('global.jbzoo_cart_config.', array()));

        // check custom success page
        $successPage = JString::trim($appParams->get('payment-page-success'));
        if (!empty($successPage)) {
            $successPage = $this->app->jbrouter->addParamsToUrl($successPage, array('order_id' => $this->order->id));
            $this->setRedirect($successPage);

            return;
        }

        if ((int)$appParams->get('manual-enabled', 0)) {

            $this->manual = $this->app->data->create(array(
                'title'   => $appParams->get('manual-title'),
                'text'    => $appParams->get('manual-text'),
                'message' => $appParams->get('manual-message'),
            ));

            $this->orderDetails->callback('paymentCallback', array(
                'date'   => $this->app->date->create()->toSQL(),
                'system' => self::TYPE_MANUAL,
            ));

            if ($appParams->get('manual-message')) {
                $this->app->jbnotify->notice($appParams->get('manual-message'));
            }

        } else {
            $this->app->jbnotify->error('Manual paymant is disabled');
        }

        // display
        $this->getview('payment_success')->addTemplatepath($this->template->getpath())->setlayout('payment_success')->display();
    }

    /**
     * Payment fail page
     */
    public function paymentFail()
    {
        $this->_init();

        $appParams = $this->app->data->create($this->appParams->get('global.jbzoo_cart_config.', array()));

        // check custom fail page
        $failPage = JString::trim($appParams->get('payment-page-fail'));
        if (!empty($failPage)) {
            $failPage = $this->app->jbrouter->addParamsToUrl($failPage, array('order_id' => $this->order->id));
            $this->setRedirect($failPage);

            return;
        }

        $this->app->document->setTitle(JText::_('JBZOO_PAYMENT_FAIL_PAGE_TITLE'));
        // display
        $this->getview('payment_fail')->addtemplatepath($this->template->getpath())->setlayout('payment_fail')->display();
    }

    /**
     * Action for success order page without payment
     */
    public function paymentNotPaid()
    {
        $this->_init();

        $appParams = $this->app->data->create($this->appParams->get('global.jbzoo_cart_config.', array()));

        // check custom success page
        $successPage = JString::trim($appParams->get('payment-page-success'));
        if (!empty($successPage)) {
            $successPage = $this->app->jbrouter->addParamsToUrl($successPage, array('order_id' => $this->order->id));
            $this->setRedirect($successPage);

            return;
        }

        $this->getview('payment_success')->addtemplatepath($this->template->getpath())->setlayout('payment_success')->display();
    }

    /**
     * Check interkassa v2 Hash
     * @param $data
     * @param $ikSecret
     * @return bool
     */
    protected function _checkIKv2Hash($data, $ikSecret)
    {
        $post = array();
        foreach ($data as $key => $value) {
            if (preg_match('#^ik_#i', $key)) {
                $post[$key] = $value;
            }
        }

        $ikSign = $post['ik_sign'];
        unset($post['ik_sign']);
        ksort($post, SORT_STRING);
        array_push($post, $ikSecret);
        $sigMd5 = base64_encode(md5(implode(':', $post), true));

        return $sigMd5 === $ikSign;
    }

}

