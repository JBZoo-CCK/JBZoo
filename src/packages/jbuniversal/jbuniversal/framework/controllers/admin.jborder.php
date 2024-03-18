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

        $this->zoo->path->register($this->zoo->path->path('jbviews:jborder'), 'renderer');
    }

    /**
     * JBZoo admin index page
     */
    public function index()
    {
        $this->zoo->toolbar->deleteList();

        $this->filter = $this->zoo->data->create($this->_jbrequest->getArray('filter', array()));

        // limit
        $this->filter['limit']  = $this->zoo->system->application->getUserStateFromRequest('global.list.limit', 'limit', $this->zoo->system->config->get('list_limit'), 'int');
        $this->filter['offset'] = $this->zoo->system->application->getUserStateFromRequest('jborder.limitstart', 'limitstart', 0, 'int');

        // order
        $this->filter['order']     = $this->_jbrequest->get('filter_order', 'id');
        $this->filter['order_dir'] = $this->_jbrequest->get('filter_order_Dir', 'desc');

        $orderModel       = JBModelOrder::model();
        $this->orderList  = $orderModel->getList($this->filter);
        $this->orderCount = $orderModel->getCount($this->filter);

        $this->pagination = $this->zoo->pagination->create($this->orderCount, $this->filter['offset'], $this->filter['limit']);

        $this->statusList = array('' => JText::_('JBZOO_ADMIN_STATUS_SELECT')) + $this->zoo->jbcartstatus->getExistsList();
        $this->userList   = array(0 => JText::_('JBZOO_ADMIN_USER_SELECT')) + $this->zoo->jbcartorder->getExistsUsers();

        $this->renderView();
    }

    /**
     *
     */
    public function edit()
    {
        $orderId = $this->zoo->request->get('cid.0', 'int');

        // get item
        $orderModel  = JBModelOrder::model();
        $this->order = $orderModel->getById($orderId);

        if ($this->zoo->jbrequest->isPost()) {
            $newData = $this->zoo->jbrequest->getArray('order');
            $this->order->updateData($newData);
            $orderModel->save($this->order);

            $editUrl = $this->zoo->jbrouter->admin(array('cid' => array($this->order->id)));
            $this->setRedirect($editUrl, JText::_('JBZOO_ADMIN_MESSAGE_SAVED'));
        }

        $this->shipRender       = $this->zoo->jbrenderer->create('Shipping');
        $this->paymentRender    = $this->zoo->jbrenderer->create('Payment');
        $this->shipFieldsRender = $this->zoo->jbrenderer->create('ShippingFields');
        $this->orderFieldRender = $this->zoo->jbrenderer->create('order');

        if ($this->order->id == 0) {
            $this->zoo->error->raiseError(500, JText::sprintf('Unable to access item with id %s', $orderId));
            return;
        }

        $this->zoo->jbassets->addVar('currencyList', $this->order->getCurrencyList());

        $this->renderView();
    }

    /**
     * Remove orders
     */
    public function remove()
    {
        //$this->zoo->session->checkToken() or jexit('Invalid Token'); // TODO fix token
        $cid = $this->zoo->request->get('cid', 'array', array());

        if (count($cid) < 1) {
            $this->zoo->jbnotify->error('JBZOO_ADMIN_ORDER_NO_SELECTED');
        }

        try {
            // delete items
            foreach ($cid as $id) {
                JBModelOrder::model()->removeById($id);
            }

            $msg = JText::_('JBZOO_ADMIN_ORDER_REMOVED');

        } catch (AppException $e) {

            // raise notice on exception
            $this->zoo->jbnotify->warning(JText::_('JBZOO_ADMIN_ORDER_DELET_ERROR') . ' (' . $e . ')');
            $msg = null;
        }

        $redirectUrl = $this->zoo->jbrouter->admin(array(
            'controller' => 'jborder',
            'task'       => 'index',
        ));

        $this->setRedirect($redirectUrl, $msg);
    }
}
