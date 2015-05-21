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

$order   = $this->order;
$jbmoney = $this->app->jbmoney;

$payment   = $order->getPayment();
$shipping  = $order->getShipping();
$modifiers = $order->getModifiersOrderPrice();

$created  = $this->app->jbdate->toHuman($order->created);
$modified = $this->app->jbdate->toHuman($order->modified);

// init JS
$this->app->html->_('behavior.tooltip');

$this->app->jbtoolbar->save();

$editUrl = $this->app->jbrouter->admin(array('cid' => array($order->id)));
?>

    <form action="<?php echo $editUrl; ?>" method="post" name="adminForm" id="adminForm" accept-charset="utf-8">

        <div class="uk-grid">
            <div class="uk-width-8-10">
                <h1>
                    <?php echo JText::sprintf('JBZOO_ORDER_TITLE', $order->getName(), '<span style="font-size: 0.6em;">' . $created . '</span>'); ?>
                </h1>
            </div>
        </div>

        <div class="uk-grid">
            <div class="uk-width-7-10 order-table">

                <?php echo $this->partial('edit_table', array(
                    'order'     => $order,
                    'shipping'  => $shipping,
                    'payment'   => $payment,
                    'modifiers' => $modifiers,
                )); ?>

                <?php echo $this->partial('edit_orderinfo', array(
                    'order' => $order,
                )); ?>
            </div>

            <div class="uk-width-3-10 order-system">

                <?php
                echo $this->partial('edit_block_basic', array(
                    'order'    => $order,
                    'created'  => $created,
                    'modified' => $modified,
                ));

                echo $this->partial('edit_block_currency', array(
                    'order' => $order,
                ));

                if ($payment) {
                    echo $this->partial('edit_block_payment', array(
                        'order'   => $order,
                        'payment' => $payment,
                    ));
                }

                if ($shipping) {
                    echo $this->partial('edit_block_shipping', array(
                        'order'    => $order,
                        'shipping' => $shipping,
                    ));
                }
                ?>
            </div>

        </div>

        <input type="hidden" name="option" value="<?php echo $this->app->jbrequest->get('option'); ?>" />
        <input type="hidden" name="cid[0]" value="<?php echo $order->id; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->app->jbrequest->getCtrl(); ?>" />
        <input type="hidden" name="task" value="edit" />
        <?php echo $this->app->html->_('form.token'); ?>
    </form>

<?php echo $this->partial('footer'); ?>