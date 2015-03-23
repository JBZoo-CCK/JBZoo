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


$order    = $this->filter->get('filter_order', 'id');
$orderDir = $this->filter->get('filter_order_Dir', 'desc');
?>

<form class="items-default uk-form" action="<?php echo $this->app->jbrouter->admin(); ?>" method="get" name="adminForm"
      id="adminForm" accept-charset="utf-8">

    <?php echo $this->partial('index_filter', array(
        'filter' => $this->filter,
    )); ?>


    <?php if ($this->pagination->total > 0) {
        echo $this->partial('index_orderlist', array(
            'orderList' => $this->orderList,
            'filter'    => $this->filter,
        ));
    } else {
        echo $this->partial('message', array(
            'title'   => JText::_('JBZOO_ADMIN_ORDER_NO_ITEMS_YET'),
            'message' => '',
        ));
    } ?>


    <input type="hidden" name="option" value="<?php echo $this->app->jbrequest->get('option'); ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->app->jbrequest->getCtrl(); ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $orderDir; ?>" />

    <?php echo $this->app->html->_('form.token'); ?>

</form>

<?php echo $this->partial('footer'); ?>
