<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class BasketJBUniversalController
 */
class BasketJBUniversalController extends JBUniversalController
{
    const SESSION_PREFIX = 'JBZOO_';

    /**
     * @var JBModelConfig
     */
    protected $_config = null;

    /**
     * @var JBCartHelper
     */
    protected $_jbcart = null;

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney = null;

    /**
     * @var JBCartOrder
     */
    public $order = null;

    /**
     * @var JBCart
     */
    public $cart = null;

    /**
     * @param array $app
     * @param array $config
     */
    public function __construct($app, $config = array())
    {
        parent::__construct($app, $config);

        //$this->app->jbdoc->noindex();
        $this->_jbmoney = $this->app->jbmoney;
        $this->_config  = JBModelConfig::model()->getGroup('cart.config');
        $this->cart     = JBcart::getInstance();
    }

    /**
     * Filter action
     */
    function index()
    {
        $this->formRenderer               = $this->app->jbrenderer->create('Order');
        $this->shippingRenderer           = $this->app->jbrenderer->create('Shipping');
        $this->paymentRenderer            = $this->app->jbrenderer->create('Payment');
        $this->shippingFieldRenderer      = $this->app->jbrenderer->create('ShippingFields');
        $this->modifierOrderPriceRenderer = $this->app->jbrenderer->create('ModifierOrderPrice');

        $this->application = $this->app->zoo->getApplication();
        $this->template    = $this->application->getTemplate();

        $this->shipping       = $this->app->jbshipping->getEnabled();
        $this->shippingFields = $this->app->jbshipping->getFields();
        $this->payment        = $this->app->jbpayment->getEnabled();
        $this->modifierPrice  = $this->app->jbmodifierprice->getEnabled();

        $this->Itemid = $this->_jbrequest->get('Itemid');
        $this->order  = $this->cart->newOrder();
        $this->items  = $this->cart->getItems();
        $this->config = $this->_config;

        $errors     = 0;
        $orderSaved = false;

        $isPaymentBtn = $this->app->jbrequest->get('create-pay');

        if ($this->_jbrequest->isPost()) {

            $formData = $this->_getRequest();

            try {

                $errors += $this->order->bind($formData);

                $errorMessages = $this->order->isValid();
                $errors += count($errorMessages);

                if ($errors) {
                    $this->app->system->application->setUserState('JBZOO_ORDDER_SUBMISSION_FORM', serialize($formData));

                    // show custom error messages
                    $this->app->jbnotify->warning($errorMessages);

                } else {
                    // saving order
                    JBModelOrder::model()->save($this->order);
                    $orderSaved = true;

                    // empty cart items
                    $this->cart->removeItems();

                    // go to payment page
                    $payment = $this->order->getPayment();
                    if ($isPaymentBtn && $payment && $paymentUrl = $payment->getRedirectUrl()) {

                        $message = $payment->getSuccessMessage();
                        if (empty($message)) {
                            $message = 'JBZOO_CART_PAYMENT_REDIRECT';
                        }

                        $this->setRedirect($paymentUrl, JText::_($message));
                    } else {
                        $this->app->jbnotify->notice(JText::_('JBZOO_CART_ORDER_SUCCESS_CREATED'));
                    }
                }

            } catch (JBCartOrderException $e) {
                $this->app->jbnotify->warning(JText::_($e->getMessage()));
            }
        }

        $this->isError = $errors;

        $templatedName = 'basket';
        if ($orderSaved) {
            $templatedName = 'basket-success';
        }

        $this
            ->getView($templatedName)
            ->addTemplatePath($this->template->getPath())
            ->setLayout($templatedName)
            ->display();
    }

    /**
     * Delete item action
     */
    public function clear()
    {
        $cart = JBcart::getInstance();
        $cart->removeItems();
        $this->app->jbajax->send();
    }

    /**
     * Delete one item from basket
     */
    public function delete()
    {
        $id  = $this->_jbrequest->get('item_id');
        $key = $this->_jbrequest->get('key');

        $cart = JBCart::getInstance();
        $cart->remove($id, null, $key);

        $recount = $cart->recount();

        $this->app->jbajax->send(array('cart' => $recount));
    }

    /**
     * Order form action
     * @throws AppException
     */
    public function form()
    {
        $orderId        = $this->_jbrequest->get('orderId');
        $order          = JBModelOrder::model()->getById($orderId);
        $this->template = $this->application->getTemplate();

        if (!$orderId || !$order->id) {
            throw new AppException('Invalid order id');
        }

        if ($order->id) {
            $payment           = $order->getPayment();
            $this->paymentForm = $payment->renderPaymentForm();

            if ($this->_jbrequest->isPost()) {
                try {

                    if ($payment->validatePaymentForm($this->_getRequest())) {
                        if ($payment && $paymentAction = $payment->actionPaymentForm()) {

                            $message = $payment->getSuccessMessage();
                            if (empty($message)) {
                                $message = 'JBZOO_CART_PAYMENT_REDIRECT';
                            }

                            $this->setRedirect($paymentAction, JText::_($message));
                        }
                    }

                } catch (AppValidatorException $e) {
                    $this->app->jbnotify->warning(JText::_($e->getMessage()));
                }
            }
        }

        $this
            ->getView('payment_form')
            ->addTemplatePath($this->template->getPath())
            ->setLayout('payment_form')
            ->display();
    }

    /**
     * Change quantity
     */
    public function quantity()
    {
        // get request
        $value = (float)$this->_jbrequest->get('value');
        $key   = $this->_jbrequest->get('key');

        $cart = JBCart::getInstance();
        if ($cart->inStock($value, $key)) {
            $cart->changeQuantity($key, $value);
            $recount = $cart->recount();

            $this->app->jbajax->send(array('cart' => $recount));
        }

        $this->app->jbajax->send(array('message' => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE_MESSAGE')), false);
    }

    /**
     *
     */
    public function shipping()
    {
        $shipping = $this->app->jbrequest->get('shipping');

        $cart = JBCart::getInstance();
        $cart->setShipping($shipping);

        $this->app->jbajax->send(array('cart' => $cart->recount()));
    }

    /**
     * Reload module action
     */
    public function reloadModule()
    {
        $moduleId = $this->_jbrequest->get('moduleId');
        $html     = $this->app->jbjoomla->renderModuleById($moduleId);

        header('Content-Type: text/html; charset=utf-8'); // fix apache default charset
        jexit($html);
    }

    /**
     * Method using to take data from element with ajax
     */
    public function callElement()
    {
        // get request
        $group     = $this->app->request->getCmd('group', '');
        $elementId = $this->app->request->getCmd('element', '');
        $orderId   = $this->app->request->getInt('order_id', '');
        $method    = $this->app->request->getCmd('method', '');
        $args      = $this->app->request->getVar('args', array(), 'default', 'array');

        if ($orderId > 0) {
            $order = JBModelOrder::model()->getById($orderId);
        } else {
            $order = JBCart::getInstance()->newOrder();
        }

        if (empty($order)) {
            return $this->app->error->raiseError(404, JText::_('Order not found'));
        }

        // get element
        if ($group == JBCart::CONFIG_SHIPPINGS) { // custom init with session data
            $element = $order->getShippingElement($elementId);
        } elseif ($group == JBCart::CONFIG_MODIFIER_ORDER_PRICE) { // custom init with session data
            $element = $order->getModifierOrderPriceElement($elementId);
        } else {
            $element = $order->getElement($elementId, $group);
        }

        if (empty($element)) {
            return $this->app->error->raiseError(404, JText::_('Element not forund'));
        }

        if (!$element->canAccess($this->app->user->get())) {
            return $this->app->error->raiseError(403, JText::_('Unable to access item'));
        }

        $element->callback($method, $args);
    }

    /**
     *
     */
    protected function _getShippingPrices()
    {
        $request  = $this->_jbrequest->get('shipping');
        $elements = $this->app->jbshipping->getEnabled();
        $result   = array();

        if (!empty($request)) {
            foreach ($request as $identifier => $data) {

                if (isset($elements[$identifier])) {

                    $service = $elements[$identifier];
                    $data    = $service->mergeParams($data);

                    $result[$identifier] = $service->getPrice($data);
                }
            }
        }

        return $result;
    }

    /**
     * Get request
     * @return array
     */
    protected function _getRequest()
    {
        $formData = $this->app->request->get('post:', 'array');

        // add _FILES data
        foreach ($_FILES as $key => $userfile) {
            if (strpos($key, 'elements_') === 0) {
                $formData[str_replace('elements_', '', $key)]['userfile'] = $userfile;
            }
        }

        return $formData;
    }

}
