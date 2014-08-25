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
 * Class JBOrderJBuniversalController
 * JBZoo tools controller for back-end
 */
class JBOrderJBuniversalController extends JBUniversalController
{

    /**
     * JBZoo admin index page
     */
    public function index()
    {
        $this->filter = $this->app->data->create($this->_jbrequest->getArray('filter', array()));
        // limit
        $this->filter['limit']  = $this->app->system->application->getUserStateFromRequest('global.list.limit', 'limit', $this->app->system->config->get('list_limit'), 'int');
        $this->filter['offset'] = $this->app->system->application->getUserStateFromRequest('jborder.limitstart', 'limitstart', 0, 'int');
        // order
        $this->filter['filter_order']     = $this->_jbrequest->get('filter_order', 'id');
        $this->filter['filter_order_Dir'] = $this->_jbrequest->get('filter_order_Dir', 'desc');

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
        $this->order = JBModelOrder::model()->getById($orderId);
        if (empty($this->order)) {
            $this->app->error->raiseError(500, JText::sprintf('Unable to access item with id %s', $orderId));
            return;
        }

        $this->renderView();
    }


}
