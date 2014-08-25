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

?>

    <form class="items-default" action="<?php echo $this->app->jbrouter->admin(); ?>" method="get" name="adminForm"
          id="adminForm" accept-charset="utf-8">

        <?php

        echo $this->partial('filter', array(
            'filter' => $this->filter,
        ));


        if ($this->pagination->total > 0) {

            echo $this->partial('orderlist', array(
                'orderList' => $this->orderList,
                'filter'    => $this->filter,
            ));

        } else {
            echo $this->partial('message', array(
                'title'   => JText::_('JBZOO_ADMIN_ORDER_NO_ITEMS_YET'),
                'message' => '',
            ));
        }

        ?>

        <?php echo $this->app->html->_('form.token'); ?>

        <input type="hidden" name="option" value="<?php echo $this->app->jbrequest->get('option'); ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->app->jbrequest->getCtrl(); ?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="filter_order" value="<?php echo $this->filter->get('filter_order', 'id'); ?>" />
        <input type="hidden" name="filter_order_Dir"
               value="<?php echo $this->filter->get('filter_order_Dir', 'desc'); ?>" />
    </form>

<?php echo $this->partial('footer'); ?>