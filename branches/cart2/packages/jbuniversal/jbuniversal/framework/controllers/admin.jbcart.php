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
 * Class JBToolsJBuniversalController
 * JBZoo tools controller for back-end
 */
class JBCartJBuniversalController extends JBUniversalController
{
    /**
     * @var JBCartElementHelper
     */
    protected $_element = null;

    /**
     * @var JBCartPositionHelper
     */
    protected $_position = null;

    /**
     * @param array $app
     * @param array $config
     */
    public function __construct($app, $config = array())
    {
        parent::__construct($app, $config);

        $this->_element  = $this->app->jbcartelement;
        $this->_position = $this->app->jbcartposition;
    }

    /**
     * Index action
     */
    public function index()
    {
        $this->renderView();
    }

    /**
     * Show payment links
     */
    public function paymentLinks()
    {
        $appId = (int)$this->_jbrequest->get('app_id');

        $this->resultUrl  = $this->app->jbrouter->payment($appId, 'callback');
        $this->successUrl = $this->app->jbrouter->payment($appId, 'success');
        $this->failUrl    = $this->app->jbrouter->payment($appId, 'fail');

        $this->app->jbdoc->disableTmpl();
        $this->renderView();
    }

    /**
     * Notification config action
     */
    public function notification()
    {
        $this->groupList = $this->_element->getGroups(array(
            JBCartOrder::ELEMENT_TYPE_NOTIFICATION,
            JBCartOrder::ELEMENT_TYPE_MODIFIERITEM
        ));

        $this->positions = $this->_position->loadPositions('notificationEvents', array(
            'order-create', 'order-edit', 'order-status', 'order-payment',
        ));

        $this->renderView();
    }

    /**
     * Modifier list action
     */
    public function modifierEvents()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_MODIFIERPRICE));
        $this->positions = $this->_position->loadPositions('modifierEvents', array(
            JBCartOrder::ELEMENT_TYPE_MODIFIERITEM,
            JBCartOrder::ELEMENT_TYPE_MODIFIERPRICE,
        ));

        $this->renderView();
    }

    /**
     * Validator list action
     */
    public function validator()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_VALIDATOR));
        $this->positions = $this->_position->loadPositions(JBCartOrder::ELEMENT_TYPE_VALIDATOR, array('list'));

        $this->renderView();
    }

    /**
     * Payment list action
     */
    public function payment()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_PAYMENT));
        $this->positions = $this->_position->loadPositions(JBCartOrder::ELEMENT_TYPE_PAYMENT, array('list'));

        $this->renderView();
    }

    /**
     * Shipping list action
     */
    public function shipping()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_SHIPPING));
        $this->positions = $this->_position->loadPositions(JBCartOrder::ELEMENT_TYPE_SHIPPING, array('list'));

        $this->renderView();
    }

    /**
     * Price param list action
     */
    public function price()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_PRICE));
        $this->priceList = $this->app->jbpriceparams->getJBPriceElements();
        $this->tempTask  = 'savePricePositions';
        $jbprice         = $this->_jbrequest->get('jbprice', key($this->priceList));

        $this->positions = $this->_position->loadPositions('price   .' . $jbprice, array('list'));

        $this->renderView();
    }

    /**
     * Validator list action
     */
    public function statusEvents()
    {
        $this->groupList = $this->_element->getGroups(array(
            JBCartOrder::ELEMENT_TYPE_NOTIFICATION,
            JBCartOrder::ELEMENT_TYPE_MODIFIERPRICE,
            JBCartOrder::ELEMENT_TYPE_MODIFIERITEM
        ));

        $this->positions = $this->_position->loadPositions('statusevents', array(
            'order-success',
            'order-paid',
            'order-canceled',
        ));

        $this->renderView();
    }

    /**
     * Cart config action
     */
    public function config()
    {
        if ($this->_jbrequest->isPost()) {
            $this->_config->setGroup('cart.config', $this->_jbrequest->getAdminForm());
            $this->setRedirect($this->app->jbrouter->admin(), JText::_('JBZOO_CONFIG_SAVED'));
        }

        $this->configData = $this->_config->getGroup('cart.config');

        $this->renderView();
    }

    /**
     * Currency list action
     */
    public function currency()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_CURRENCY));
        $this->positions = $this->_position->loadPositions(JBCartOrder::ELEMENT_TYPE_CURRENCY, array('list'));

        $this->renderView();
    }

    /**
     * Status list action
     */
    public function status()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_STATUS));
        $this->positions = $this->_position->loadPositions(JBCartOrder::ELEMENT_TYPE_STATUS, array('list'));

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function fields()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_ORDER));
        $this->positions = $this->_position->loadPositions(JBCartOrder::ELEMENT_TYPE_ORDER, array('list'));

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function emailTmpl()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_ORDER));
        $this->positions = $this->_position->loadPositions(JBCartOrder::ELEMENT_TYPE_ORDER, array('list'));

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function cartTmpl()
    {
        $layout = $this->_jbrequest->get('layout', 'default');

        $renderer = $this->app->jbrenderer->create('order');

        $this->layoutList     = $renderer->getLayouts('order');
        $this->positionList   = $renderer->getPositions('order.' . $layout);
        $this->dragElements   = $this->_position->loadElements(JBCartOrder::ELEMENT_TYPE_ORDER);
        $this->elementsParams = $this->_position->loadParams('cartTmpl.' . $layout);
        $this->positions      = $this->_position->loadPositionsTmpl('cartTmpl.' . $layout, JBCartOrder::ELEMENT_TYPE_ORDER, $this->positionList);

        $this->renderView();
    }

    /**
     *
     */
    public function jbpriceFilterTmpl()
    {
        $layout = $this->_jbrequest->get('layout', 'default');

        $renderer           = $this->app->jbrenderer->create('jbpricefilter');
        $this->layoutList   = $renderer->getLayouts('jbpricefilter');
        $this->positionList = $renderer->getPositions('jbpricefilter.' . $layout);

        $jbprice = $this->_jbrequest->get('jbprice');

        $this->tempTask       = 'savePricePositions';
        $this->priceList      = $this->app->jbpriceparams->getJBPriceElements();
        $this->systemElements = $this->_element->getSystemTmpl('price');
        $this->dragElements   = $this->_position->loadElements('priceparams');
        $this->elementsParams = $this->_position->loadParams('jbpriceFilterTmpl.' . $jbprice . '.' . $layout);

        $this->positions = $this->_position->loadPositionsTmpl('jbpriceFilterTmpl.' . $jbprice . '.' . $layout, 'priceparams', $this->positionList);
        //dump($this->positions);

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function jbpriceTmpl()
    {
        $layout = $this->_jbrequest->get('layout', 'default');
        $id     = null;

        $jbprice = $this->_jbrequest->get('jbprice');

        $renderer = $this->app->jbrenderer->create('jbprice');

        $this->priceList    = $this->app->jbpriceparams->getJBPriceElements();
        $this->layoutList   = $renderer->getLayouts('jbprice');
        $this->positionList = $renderer->getPositions('jbprice.' . $layout);
        $this->tempTask     = 'savePricePositions';

        $this->systemElements = $this->_element->getSystemTmpl('price');
        $this->dragElements   = $this->_position->loadElements(JBCartOrder::ELEMENT_TYPE_PRICE);
        $this->elementsParams = $this->_position->loadParams('jbpriceTmpl.' . $jbprice . '.' . $layout);

        $this->positions = $this->_position->loadPositionsTmpl('jbpriceTmpl.' . $jbprice . '.' . $layout, 'priceparams', $this->positionList);
        //dump($this->positions);

        $this->renderView();
    }

    /**
     * Add new element row (ajax calling)
     */
    public function addElement()
    {
        // get request vars
        $elementType  = $this->_jbrequest->getWord('elementType');
        $elementGroup = $this->_jbrequest->getWord('elementGroup');

        // load element
        $this->element             = $this->_element->create($elementType, $elementGroup);
        $this->element->identifier = $this->app->utility->generateUUID();

        if ($this->element->getMetaData('core') == 'true') {
            $this->element->identifier = '_' . strtolower($elementType);
        }

        $this->app->jbdoc->disableTmpl();
        $this->renderView();
    }

    /**
     * Shipping fields
     */
    public function shippingField()
    {
        $this->groupList = $this->_element->getGroups(array(JBCartOrder::ELEMENT_TYPE_SHIPPINGFIELD));
        $this->positions = $this->_position->loadPositions(JBCartOrder::ELEMENT_TYPE_SHIPPINGFIELD, array('list'));

        $this->renderView();
    }

    /**
     * Custom save action for any positions data
     */
    public function savePositions()
    {
        // session token
        $this->app->session->checkToken() or jexit('Invalid Token');

        $defaultRedirect = $this->app->jbrouter->admin(array('task' => 'index'));
        if (!$this->_jbrequest->isPost()) {
            $this->setRedirect($defaultRedirect);
        }

        $positions = $this->_jbrequest->getArray('positions');
        $group     = $this->_jbrequest->get('group');
        $layout    = $this->_jbrequest->get('layout');
        $redirect  = $this->_jbrequest->get('redirect', $defaultRedirect);

        $this->_position->save($group, $positions, $layout);

        $this->setRedirect($redirect, JText::_('JBZOO_ADMIN_MESSAGE_SAVED'));
    }

    /**
     * Custom save action for any positions data
     */
    public function savePricePositions()
    {
        // session token
        $this->app->session->checkToken() or jexit('Invalid Token');

        $defaultRedirect = $this->app->jbrouter->admin(array('task' => 'index'));
        if (!$this->_jbrequest->isPost()) {
            $this->setRedirect($defaultRedirect);
        }

        $positions = $this->_jbrequest->getArray('positions');
        $group     = $this->_jbrequest->get('group');
        $layout    = $this->_jbrequest->get('layout');
        $jbprice   = $this->_jbrequest->get('jbprice');
        $redirect  = $this->_jbrequest->get('redirect', $defaultRedirect);

        $this->_position->savePrice($group, $positions, $layout, $jbprice);

        $this->setRedirect($redirect, JText::_('JBZOO_ADMIN_MESSAGE_SAVED'));
    }

}
