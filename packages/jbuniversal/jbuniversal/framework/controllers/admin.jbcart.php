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

        // default
        $this->element  = $this->app->jbrequest->get('element');
        $this->layout   = $this->app->jbrequest->get('layout');
        $this->saveTask = 'savePositions';
    }

    /**
     * Index action
     */
    public function index()
    {
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
            JBCart::ELEMENT_TYPE_NOTIFICATION,
            JBCart::ELEMENT_TYPE_MODIFIERITEM
        ));

        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_NOTIFICATION, array(
            JBCart::NOTIFY_ORDER_CREATE,
            JBCart::NOTIFY_ORDER_EDIT,
            JBCart::NOTIFY_ORDER_STATUS,
            JBCart::NOTIFY_ORDER_PAYMENT,
        ));

        $this->groupKey = JBCart::CONFIG_NOTIFICATION;
        $this->renderView();
    }

    /**
     * Modifier list action
     */
    public function modifier()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_MODIFIERPRICE));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_MODIFIERS, array(
            JBCart::ELEMENT_TYPE_MODIFIERPRICE,
            JBCart::ELEMENT_TYPE_MODIFIERITEM,
        ));

        $this->groupKey = JBCart::CONFIG_MODIFIERS;
        $this->renderView();
    }

    /**
     * Validator list action
     */
    public function validator()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_VALIDATOR));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_VALIDATORS, array(JBCart::DEFAULT_POSITION));

        $this->groupKey = JBCart::CONFIG_VALIDATORS;
        $this->renderView();
    }

    /**
     * Payment list action
     */
    public function payment()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_PAYMENT));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_PAYMENTS, array(JBCart::DEFAULT_POSITION));

        $this->groupKey = JBCart::CONFIG_PAYMENTS;
        $this->renderView();
    }

    /**
     * Shipping list action
     */
    public function shipping()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_SHIPPING));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_SHIPPINGS, array(JBCart::DEFAULT_POSITION));

        $this->groupKey = JBCart::CONFIG_SHIPPINGS;
        $this->renderView();
    }

    /**
     * Price param list action
     */
    public function price()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_PRICE));
        $this->priceList = $this->app->jbpriceparams->getJBPriceElements();

        $element         = $this->_jbrequest->get('element', key($this->priceList));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_PRICE . '.' . $element, array(JBCart::DEFAULT_POSITION));

        $this->groupKey = JBCart::CONFIG_PRICE;
        $this->saveTask = 'saveElementPositions';

        $this->renderView();
    }

    /**
     * Validator list action
     */
    public function statusEvents()
    {
        $this->groupList = $this->_element->getGroups(array(
            JBCart::ELEMENT_TYPE_NOTIFICATION,
            JBCart::ELEMENT_TYPE_MODIFIERPRICE,
            JBCart::ELEMENT_TYPE_MODIFIERITEM
        ));

        $statusList      = $this->app->jbcartstatus->getList();
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_STATUS_EVENTS, array_keys($statusList));

        $this->groupKey = JBCart::CONFIG_STATUS_EVENTS;
        $this->renderView();
    }

    /**
     * Currency list action
     */
    public function currency()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_CURRENCY));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_CURRENCIES, array(JBCart::DEFAULT_POSITION));
        $this->groupKey  = JBCart::CONFIG_CURRENCIES;

        $this->renderView();
    }

    /**
     * Status list action
     */
    public function status()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_STATUS));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_STATUSES, array(JBCart::DEFAULT_POSITION));

        $this->groupKey = JBCart::CONFIG_STATUSES;
        $this->renderView();
    }

    /**
     * Field list action
     */
    public function fields()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_ORDER));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_FIELDS, array(JBCart::DEFAULT_POSITION));
        $this->groupKey  = JBCart::CONFIG_FIELDS;

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function emailTmpl()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_ORDER));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_EMAIL_TMPL, array(JBCart::DEFAULT_POSITION));
        $this->groupKey  = JBCart::CONFIG_EMAIL_TMPL;

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function cartTmpl()
    {
        $renderer         = $this->app->jbrenderer->create('order');
        $this->layoutList = $renderer->getLayouts('order');

        $this->layout = $this->_jbrequest->get('layout', key($this->layoutList));

        $this->positionList   = $renderer->getPositions('order.' . $this->layout);
        $this->dragElements   = $this->_position->loadElements(JBCart::CONFIG_FIELDS);
        $this->elementsParams = $this->_position->loadParams(JBCart::CONFIG_FIELDS_TMPL . '.' . $this->layout);
        $this->positions      = $this->_position->loadPositionsTmpl(JBCart::CONFIG_FIELDS_TMPL . '.' . $this->layout, JBCart::CONFIG_FIELDS, $this->positionList);

        $this->groupKey = JBCart::CONFIG_FIELDS_TMPL;

        $this->renderView();
    }

    /**
     *
     */
    public function jbpriceFilterTmpl()
    {
        $renderer = $this->app->jbrenderer->create('jbpricefilter');

        $this->elementList = $this->app->jbpriceparams->getJBPriceElements();
        $this->layoutList  = $renderer->getLayouts('jbpricefilter');

        $this->layout  = $this->_jbrequest->get('layout', key($this->layoutList));
        $this->element = $this->_jbrequest->get('element', key($this->elementList));

        $this->positionList = $renderer->getPositions('jbpricefilter.' . $this->layout);

        $this->systemElements = $this->_element->getSystemTmpl('price');
        $this->dragElements   = $this->_position->loadElements('price');

        $confName             = JBCart::CONFIG_PRICE_TMPL_FILTER . '.' . $this->element . '.' . $this->layout;
        $this->elementsParams = $this->_position->loadParams($confName);
        $this->positions      = $this->_position->loadPositionsTmpl($confName, 'priceparams', $this->positionList);

        $this->saveTask = 'saveElementPositions';
        $this->groupKey = JBCart::CONFIG_PRICE_TMPL_FILTER;
        $this->renderView();
    }

    /**
     * Field list action
     */
    public function jbpriceTmpl()
    {
        $renderer = $this->app->jbrenderer->create('jbprice');

        $this->elementList = $this->app->jbpriceparams->getJBPriceElements();
        $this->layoutList  = $renderer->getLayouts('jbprice');

        $this->layout  = $this->_jbrequest->get('layout', key($this->layoutList));
        $this->element = $this->_jbrequest->get('element', key($this->elementList));

        $this->positionList = $renderer->getPositions('jbprice.' . $this->layout);

        $this->dragElements   = $this->_position->loadElements(JBCart::ELEMENT_TYPE_PRICE);
        $this->systemElements = $this->_element->getSystemTmpl('price');

        $confName             = JBCart::CONFIG_PRICE_TMPL . '.' . $this->element . '.' . $this->layout;
        $this->elementsParams = $this->_position->loadParams($confName);
        $this->positions      = $this->_position->loadPositionsTmpl($confName, 'priceparams', $this->positionList);

        $this->saveTask = 'saveElementPositions';
        $this->groupKey = JBCart::CONFIG_PRICE_TMPL;
        $this->renderView();
    }

    /**
     * Shipping fields
     */
    public function shippingField()
    {
        $this->groupList = $this->_element->getGroups(array(JBCart::ELEMENT_TYPE_SHIPPINGFIELD));
        $this->positions = $this->_position->loadPositions(JBCart::CONFIG_SHIPPINGFIELDS, array(JBCart::DEFAULT_POSITION));
        $this->groupKey  = JBCart::CONFIG_SHIPPINGFIELDS;

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
    public function saveElementPositions()
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
        $element   = $this->_jbrequest->get('element');
        $redirect  = $this->_jbrequest->get('redirect', $defaultRedirect);

        $this->_position->savePrice($group, $positions, $layout, $element);

        $this->setRedirect($redirect, JText::_('JBZOO_ADMIN_MESSAGE_SAVED'));
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

}
