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
 * Class ClientareaJBUniversalController
 */
class ClientareaJBUniversalController extends JBUniversalController
{
    /**
     * @type JUser
     */
    protected $_user = null;

    /**
     * Controller init
     */
    public function init()
    {
        $this->app->jbdoc->noindex();
        $this->application = $this->app->zoo->getApplication();
        $this->template    = $this->application->getTemplate();

        $this->_user = JFactory::getUser();
        if (empty($this->_user->id)) {
            $url = 'index.php?option=com_users&view=login&return=' . base64_encode($this->app->jbenv->getCurrentUrl());
            $this->setRedirect($url, JText::_('JBZOO_CLIENTAREA_NEED_LOGIN'));
            return;
        }

    }

    /**
     * Order list
     */
    public function orders()
    {
        $this->init();

        $user = JFactory::getUser();
        if (empty($user->id)) {
            $url = 'index.php?option=com_users&view=login&return=' . base64_encode($this->app->jbenv->getCurrentUrl());
            $this->setRedirect($url, JText::_('JBZOO_CLIENTAREA_NEED_LOGIN'));
            return;
        }

        $this->orders = JBModelOrder::model()->getList(array(
            'created_by' => $user->id,
            'field'      => 'id',
            'dir'        => 'DESC',
        ));

        $this
            ->getView('clientarea_orders')
            ->addTemplatePath($this->template->getPath())
            ->setLayout('clientarea_orders')
            ->display();
    }

    /**
     * Order list
     */
    public function order()
    {
        $this->init();

        $orderId     = $this->app->jbrequest->get('order_id');
        $this->order = JBModelOrder::model()->getById($orderId);

        $this->formRenderer           = $this->app->jbrenderer->create('Order');
        $this->shippingRenderer       = $this->app->jbrenderer->create('Shipping');
        $this->shippingFieldsRenderer = $this->app->jbrenderer->create('ShippingFields');

        if (!$this->order || ($this->_user->id != $this->order->created_by)) {
            $this->app->jbnotify->error('JBZOO_CLIENTAREA_ORDER_NOT_FOUND');
        }

        $this
            ->getView('clientarea_order')
            ->addTemplatePath($this->template->getPath())
            ->setLayout('clientarea_order')
            ->display();
    }
}
