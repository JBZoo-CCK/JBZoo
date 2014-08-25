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

$order = $this->order;
$jbmoney = $this->app->jbmoney;

$payment = $order->getPayment();
$shipping = $order->getShipping();

$created = $this->app->html->_('date', $order->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());
$modified = $this->app->html->_('date', $order->modified, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());

?>

    <form action="<?php echo $this->app->jbrouter->admin(); ?>" method="get" name="adminForm" id="adminForm"
          accept-charset="utf-8">

        <div class="uk-grid">
            <div class="uk-width-8-10">
                <h1>Заказ №<?php echo $order->getName(); ?>
                    <span style="font-size: 0.6em"> от <?php echo $created; ?></span></h1>
            </div>
        </div>

        <div class="uk-grid">
            <div class="uk-width-7-10">

                <?php echo $this->partial('edit_table', array(
                    'order' => $order,
                ));?>

                <?php echo $this->partial('edit_orderinfo', array(
                    'order' => $order,
                ));?>
            </div>

            <div class="uk-width-3-10">

                <?php

                echo $this->partial('edit_block_basic', array(
                    'order'    => $order,
                    'created'  => $created,
                    'modified' => $modified,
                ));

                echo $this->partial('edit_block_payment', array(
                    'order'   => $order,
                    'payment' => $payment,
                ));

                echo $this->partial('edit_block_shipping', array(
                    'order'    => $order,
                    'shipping' => $shipping,
                ));

                ?>
            </div>

        </div>

        <input type="hidden" name="option" value="<?php echo $this->app->jbrequest->get('option'); ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->app->jbrequest->getCtrl(); ?>" />
        <input type="hidden" name="task" value="edit" />
        <?php echo $this->app->html->_('form.token'); ?>
    </form>

<?php echo $this->partial('footer'); ?>