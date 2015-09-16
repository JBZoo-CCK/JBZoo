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
 * Class JBOrderJBuniversalController
 * JBZoo tools controller for back-end
 */
class JBOrderJBuniversalController extends JBUniversalController
{
    /**
     * @param array $app
     * @param array $config
     */
    public function __construct($app, $config = array())
    {
        parent::__construct($app, $config);

        $this->app->path->register($this->app->path->path('jbviews:jborder'), 'renderer');
    }

    /**
     * JBZoo admin index page
     */
    public function index()
    {
        $this->app->toolbar->deleteList();

        $this->filter = $this->app->data->create($this->_jbrequest->getArray('filter', array()));

        // limit
        $this->filter['limit']  = $this->app->system->application->getUserStateFromRequest('global.list.limit', 'limit', $this->app->system->config->get('list_limit'), 'int');
        $this->filter['offset'] = $this->app->system->application->getUserStateFromRequest('jborder.limitstart', 'limitstart', 0, 'int');

        // order
        $this->filter['order']     = $this->_jbrequest->get('filter_order', 'id');
        $this->filter['order_dir'] = $this->_jbrequest->get('filter_order_Dir', 'desc');

        $orderModel       = JBModelOrder::model();
        $this->orderList  = $orderModel->getList($this->filter);
        $this->orderCount = $orderModel->getCount($this->filter);

        $this->pagination = $this->app->pagination->create($this->orderCount, $this->filter['offset'], $this->filter['limit']);

        $this->statusList = array('' => JText::_('JBZOO_ADMIN_STATUS_SELECT')) + $this->app->jbcartstatus->getExistsList();
        $this->userList   = array(0 => JText::_('JBZOO_ADMIN_USER_SELECT')) + $this->app->jbcartorder->getExistsUsers();

        $this->renderView();
    }

    /**
     *
     */
    public function edit()
    {
        $orderId = $this->app->request->get('cid.0', 'int');

        // get item
        $orderModel  = JBModelOrder::model();
        $this->order = $orderModel->getById($orderId);

        if ($this->app->jbrequest->isPost()) {
            $newData = $this->app->jbrequest->getArray('order');
            $this->order->updateData($newData);
            $orderModel->save($this->order);

            $editUrl = $this->app->jbrouter->admin(array('cid' => array($this->order->id)));
            $this->setRedirect($editUrl, JText::_('JBZOO_ADMIN_MESSAGE_SAVED'));
        }

        $this->shipRender       = $this->app->jbrenderer->create('Shipping');
        $this->paymentRender    = $this->app->jbrenderer->create('Payment');
        $this->shipFieldsRender = $this->app->jbrenderer->create('ShippingFields');
        $this->orderFieldRender = $this->app->jbrenderer->create('order');

        if ($this->order->id == 0) {
            $this->app->error->raiseError(500, JText::sprintf('Unable to access item with id %s', $orderId));
            return;
        }

        $this->app->jbassets->addVar('currencyList', $this->order->getCurrencyList());

        $this->renderView();
    }

    /**
     * Remove orders
     */
    public function remove()
    {
        //$this->app->session->checkToken() or jexit('Invalid Token'); // TODO fix token
        $cid = $this->app->request->get('cid', 'array', array());

        if (count($cid) < 1) {
            $this->app->jbnotify->error('JBZOO_ADMIN_ORDER_NO_SELECTED');
        }

        try {
            // delete items
            foreach ($cid as $id) {
                JBModelOrder::model()->removeById($id);
            }

            $msg = JText::_('JBZOO_ADMIN_ORDER_REMOVED');

        } catch (AppException $e) {

            // raise notice on exception
            $this->app->jbnotify->warning(JText::_('JBZOO_ADMIN_ORDER_DELET_ERROR') . ' (' . $e . ')');
            $msg = null;
        }

        $redirectUrl = $this->app->jbrouter->admin(array(
            'controller' => 'jborder',
            'task'       => 'index',
        ));

        $this->setRedirect($redirectUrl, $msg);
    }
}
