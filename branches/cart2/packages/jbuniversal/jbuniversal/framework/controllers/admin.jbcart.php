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
    protected $_jbcartelement = null;

    /**
     * @var JBCartPositionHelper
     */
    protected $_jbcartposition = null;

    /**
     * @param array $app
     * @param array $config
     */
    public function __construct($app, $config = array())
    {
        parent::__construct($app, $config);

        $this->_jbcartelement  = $this->app->jbcartelement;
        $this->_jbcartposition = $this->app->jbcartposition;
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
        $this->groupList = $this->_jbcartelement->getGroups(array('notification', 'modifieritem'));
        $this->positions = $this->_jbcartposition->load('notification', array(
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
        $this->groupList = $this->_jbcartelement->getGroups(array('modifierprice'));
        $this->positions = $this->_jbcartposition->load('modifierprice', array(
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
        $this->groupList = $this->_jbcartelement->getGroups(array('validator'));
        $this->positions = $this->_jbcartposition->load('validator', array('before-create'));

        $this->renderView();
    }

    /**
     * Payment list action
     */
    public function payment()
    {
        $this->groupList = $this->_jbcartelement->getGroups(array('payment'));
        $this->positions = $this->_jbcartposition->load('payment', array('list'));

        $this->renderView();
    }

    /**
     * Delivery list action
     */
    public function delivery()
    {
        $this->groupList = $this->_jbcartelement->getGroups(array('delivery'));
        $this->positions = $this->_jbcartposition->load('delivery', array('list'));

        $this->renderView();
    }

    /**
     * Price param list action
     */
    public function priceParams()
    {
        $this->groupList = $this->_jbcartelement->getGroups(array('price'));
        $this->positions = $this->_jbcartposition->load('priceParams', array('list'));

        $this->renderView();
    }

    /**
     * Validator list action
     */
    public function statusEvents()
    {
        $this->groupList = $this->_jbcartelement->getGroups(array('notification', 'modifierprice', 'modifieritem'));
        $this->positions = $this->_jbcartposition->load('statusevents', array(
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
        $this->groupList = $this->_jbcartelement->getGroups(array('currency'));
        $this->positions = $this->_jbcartposition->load('currency', array('list'));

        $this->renderView();
    }

    /**
     * Status list action
     */
    public function status()
    {
        $this->groupList = $this->_jbcartelement->getGroups(array('status'));
        $this->positions = $this->_jbcartposition->load('status', array('list'));

        $this->renderView();
    }

    /**
     * Field list action
     */
    public function orderfields()
    {
        $this->groupList = $this->_jbcartelement->getGroups(array('order'));
        $this->positions = $this->_jbcartposition->load('orderfields', array('list'));

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
        $this->element             = $this->_jbcartelement->create($elementType, $elementGroup);
        $this->element->identifier = $this->app->utility->generateUUID();

        $this->app->jbdoc->disableTmpl();
        $this->renderView();
    }

    /**
     * Custom save action for any positions data
     */
    public function savePositions()
    {
        $defaultRedirect = $this->app->jbrouter->admin(array('task' => 'index'));
        if (!$this->_jbrequest->isPost()) {
            $this->setRedirect($defaultRedirect);
        }

        $positions = $this->_jbrequest->getArray('positions');
        $group     = $this->_jbrequest->get('group');
        $redirect  = $this->_jbrequest->get('redirect', $defaultRedirect);

        $this->_jbcartposition->save($group, $positions);

        $this->setRedirect($redirect, JText::_('JBZOO_ADMIN_MESSAGE_SAVED'));
    }

}
