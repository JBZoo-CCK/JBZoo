<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$emptyRow = '<tr class="empty-row"><td colspan="50"></td></tr>';

$this->sum   = $order->val();
$this->count = 0;
$currency    = $this->sum->cur();
?>

<h2>
    <?php echo JText::_('JBZOO_ORDER_ITEMS_LIST'); ?>
</h2>

<table class="uk-table uk-table-condensed jborder-details-table">
    <thead>
    <tr>
        <th class="col-image"><?php echo JText::_('JBZOO_ORDER_ITEM_IMAGE'); ?></th>
        <th class="col-name"><?php echo JText::_('JBZOO_ORDER_ITEM_NAME'); ?></th>
        <th class="col-item"><?php echo JText::_('JBZOO_ORDER_PRICE_PER_ITEM'); ?></th>
        <th class="col-quantity"><?php echo JText::_('JBZOO_ORDER_ITEM_QUANTITY'); ?></th>
        <th class="col-total"><?php echo JText::_('JBZOO_ORDER_ITEM_TOTAL'); ?></th>
    </tr>
    </thead>

    <tbody>
    <?php echo $this->partial('edit_table_tbody', array(
        'order'    => $order,
        'items'    => $order->getItems(),
        'currency' => $currency
    ));

    $this->sum = $order->getTotalForItems(false);

    ?>
    </tbody>

    <tfoot>

    <tr>
        <td class="noborder-btm"></td>
        <td colspan="2"><p><?php echo JText::_('JBZOO_ORDER_SUBTOTAL'); ?></p></td>
        <td class="align-right"><p><?php echo $this->count; ?></p></td>
        <td class="align-right subtotal-money"><?php echo $this->sum->html(); ?>
    </tr>
    <?php echo $emptyRow; ?>

    <?php echo $this->partial('edit_table_modifiers', array(
        'order'    => $order,
        'currency' => $currency
    )); ?>

    <?php if ($shipping || $payment || $modifiers) : ?>
        <tr>
            <td class="noborder-btm"></td>
            <td colspan="3"><p><?php echo JText::_('JBZOO_ORDER_SUBTOTAL'); ?></p></td>
            <td class="align-right subtotal-money"><?php echo $this->sum->html(); ?></td>
        </tr>
        <?php echo $emptyRow; ?>
    <?php endif; ?>

    <?php echo $this->partial('edit_table_payment', array(
        'order'    => $order,
        'payment'  => $payment,
        'currency' => $currency
    )); ?>

    <?php echo $this->partial('edit_table_shipping', array(
        'order'    => $order,
        'shipping' => $shipping,
        'currency' => $currency
    )); ?>

    <?php if ($shipping || $payment) : ?>
        <tr>
            <td class="noborder-btm"></td>
            <td colspan="3"><p><?php echo JText::_('JBZOO_ORDER_SUBTOTAL'); ?></p></td>
            <td class="align-right subtotal-money"><?php echo $this->sum->html(); ?></td>
        </tr>
        <?php echo $emptyRow; ?>
    <?php endif; ?>

    <tr>
        <td colspan="2" class="noborder-btm"></td>
        <td class="total-name" colspan="2"><?php echo JText::_('JBZOO_ORDER_TOTALPRICE'); ?></td>
        <td class="total-value"><?php echo $this->sum->html(); ?></td>
    </tr>
    </tfoot>

</table>

<?php // dump($this->sum->logs()); ?>