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
    protected $_cart = null;

    /**
     * @param array $app
     * @param array $config
     */
    public function __construct($app, $config = array())
    {
        parent::__construct($app, $config);

        $this->app->jbdoc->noindex();
        $this->_jbmoney = $this->app->jbmoney;
        $this->_config  = JBModelConfig::model()->getGroup('cart');
        $this->_cart    = JBcart::getInstance();
    }

    /**
     * Filter action
     */
    function index()
    {
        $this->formRenderer          = $this->app->jbrenderer->create('OrderSubmission');
        $this->shippingRenderer      = $this->app->jbrenderer->create('Shipping');
        $this->paymentRenderer       = $this->app->jbrenderer->create('Payment');
        $this->shippingFieldRenderer = $this->app->jbrenderer->create('ShippingFields');

        $this->application = $this->app->zoo->getApplication();
        $this->template    = $this->application->getTemplate();

        $this->shipping       = $this->app->jbshipping->getEnabled();
        $this->shippingFields = $this->app->jbshipping->getFields();
        $this->payment        = $this->app->jbpayment->getEnabled();

        $this->Itemid = $this->_jbrequest->get('Itemid');
        $this->order  = $this->_cart->newOrder();

        $errors     = 0;
        $orderSaved = false;

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
                    $this->app->event->dispatcher->notify($this->app->event->create($this->order, 'basket:beforesave', array()));
                    JBModelOrder::model()->save($this->order);
                    $this->app->event->dispatcher->notify($this->app->event->create($this->order, 'basket:aftersave', array()));

                    //dump(JBModelOrder::model()->getById($this->order->id));

                    // empty cart items
                    $this->_cart->removeItems();

                    // go to payment page
                    $payment = $this->order->getPayment();
                    if ($payment && $paymentUrl = $payment->getRedirectUrl()) {
                        $this->setRedirect($paymentUrl, JText::_('JBZOO_CART_PAYMENT_REDIRECT'));
                    }

                    $orderSaved = true;
                    $this->app->jbnotify->notice(JText::_('JBZOO_CART_ORDER_SUCCESS_CREATED'));
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
        $this->app->jbcart->removeItems();
        $this->app->jbajax->send();
    }

    /**
     * Method using to take data from element with ajax
     */
    public function callElement()
    {
        $element  = $this->app->request->getCmd('element', '');
        $method   = $this->app->request->getCmd('method', '');
        $args     = $this->app->request->getVar('args', array(), 'default', 'array');
        $services = $this->app->data->create($this->app->jbshipping->getEnabled());

        // get element and execute callback method
        if ($element = $services->get($element)) {
            $element->callback($method, $args);
        }
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
