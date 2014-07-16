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
    public function notificationEvents()
    {
        $this->groupList = $this->_element->getGroups(array('notification', 'modifieritem'));
        $this->positions = $this->_position->loadPostions('notificationEvents', array(
            'order-create',
            'order-edit',
            'order-status',
            'order-payment',
        ));

        $this->renderView();
    }

    /**
     * Modifier list action
     */
    public function modifierEvents()
    {
        $this->groupList = $this->_element->getGroups(array('modifierprice'));
        $this->positions = $this->_position->loadPostions('modifierEvents', array(
            'taxes',
            'discounts',
        ));

        $this->renderView();
    }

    /**
     * Validator list action
     */
    public function validatorEvents()
    {
        $this->groupList = $this->_element->getGroups(array('validator'));
        $this->positions = $this->_position->loadPostions('validatorEvents', array('before-create'));

        $this->renderView();
    }

    /**
     * Payment list action
     */
    public function payment()
    {
        $this->groupList = $this->_element->getGroups(array('payment'));
        $this->positions = $this->_position->loadPostions('payment', array('list'));

        $this->renderView();
    }

    /**
     * Delivery list action
     */
    public function delivery()
    {
        $this->groupList = $this->_element->getGroups(array('delivery'));
        $this->positions = $this->_position->loadPostions('delivery', array('list'));

        $this->renderView();
    }

    /**
     * Price param list action
     */
    public function priceParams()
    {
        $this->groupList = $this->_element->getGroups(array('price'));
        $this->positions = $this->_position->loadPostions('priceParams', array('list'));

        $this->renderView();
    }

    /**
     * Validator list action
     */
    public function statusEvents()
    {
        $this->groupList = $this->_element->getGroups(array('notification', 'modifierprice', 'modifieritem'));
        $this->positions = $this->_position->loadPostions('statusevents', array(
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
        $this->groupList = $this->_element->getGroups(array('currency'));
        $this->positions = $this->_position->loadPostions('currency', array('list'));

        $this->renderView();
    }

    /**
     * Status list action
     */
    public function status()
    {
        $this->groupList = $this->_element->getGroups(array('status'));
        $this->positions = $this->_position->loadPostions('status', array('list'));

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function fields()
    {
        $this->groupList = $this->_element->getGroups(array('order'));
        $this->positions = $this->_position->loadPostions('fields', array('list'));

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function emailTmpl()
    {
        $this->groupList = $this->_element->getGroups(array('order'));
        $this->positions = $this->_position->loadPostions('fields', array('list'));

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
        $this->dragElements   = $this->_position->loadElements('fields');
        $this->elementsParams = $this->_position->loadParams('cartTmpl.' . $layout);
        $this->positions      = $this->_position->loadPostionsTmpl('cartTmpl.' . $layout, 'fields', $this->positionList);

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function jbpriceTmpl()
    {
        $layout = $this->_jbrequest->get('layout', 'default');

        $renderer           = $this->app->jbrenderer->create('jbprice');
        $this->layoutList   = $renderer->getLayouts('jbprice');
        $this->positionList = $renderer->getPositions('jbprice.' . $layout);

        $this->systemElements = $this->_element->getSystemTmpl('price');
        $this->dragElements   = $this->_position->loadElements('priceparams');
        $this->elementsParams = $this->_position->loadParams('jbpriceTmpl.' . $layout);
        $this->positions      = $this->_position->loadPostionsTmpl('jbpriceTmpl.' . $layout, 'priceparams', $this->positionList);

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

}
