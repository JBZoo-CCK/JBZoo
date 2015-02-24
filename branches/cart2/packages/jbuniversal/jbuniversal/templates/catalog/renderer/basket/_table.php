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

$this->app->jbassets->less('jbassets:less/cart/table.less');

$string = $this->app->jbstring;
$jbhtml = $this->app->jbhtml;

$cart   = JBCart::getInstance();
$order  = $cart->newOrder();
$config = $cart->getCofigs();

$colspan = 2
    + $config->get('tmpl_image_show', 1)
    + $config->get('tmpl_price4one', 1)
    + $config->get('tmpl_quntity', 1)
    + $config->get('tmpl_subtotal', 1);

?>

<table class="jbcart-table jsJBZooCartTable">
    <thead>
    <tr>
        <?php if ($config->get('tmpl_image_show', 1)) : ?>
            <th class="jbcart-col jbcart-col-image"></th>
        <?php endif; ?>

        <th class="jbcart-col jbcart-col-name"><?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?></th>

        <?php if ($config->get('tmpl_price4one', 1)) : ?>
            <th class="jbcart-col jbcart-col-price"><?php echo JText::_('JBZOO_CART_ITEM_PRICE'); ?></th>
        <?php endif; ?>

        <?php if ($config->get('tmpl_quntity', 1)) : ?>
            <th class="jbcart-col jbcart-col-quantity"><?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?></th>
        <?php endif; ?>

        <?php if ($config->get('tmpl_subtotal', 1)) : ?>
            <th class="jbcart-col jbcart-col-subtotal"><?php echo JText::_('JBZOO_CART_ITEM_SUBTOTAL'); ?></th>
        <?php endif; ?>

        <th class="jbcart-col jbcart-col-delete"></th>
    </tr>
    </thead>
    <tbody>

    <tr class="jbcart-row-empty">
        <td colspan="<?php echo $colspan; ?>" class="jbcart-cell-empty"></td>
    </tr>

    <?php
    foreach ($view->itemsHtml as $itemKey => $itemHtml) : ?>
        <tr class="jbcart-row jsCartTableRow js<?php echo $itemKey; ?>" data-key="<?php echo $itemKey; ?>">

            <?php if ($config->get('tmpl_image_show', 1)) : ?>
                <td class="jbcart-image"><?php echo $itemHtml['image']; ?></td>
            <?php endif; ?>

            <td class="jbcart-name">
                <?php echo $itemHtml['name']; ?>
                <?php if ($config->get('tmpl_sku_show', 1)) {
                    echo $itemHtml['sku'];
                } ?>
                <?php echo $itemHtml['params']; ?>
            </td>

            <?php if ($config->get('tmpl_price4one', 1)) : ?>
                <td class="jbcart-price"><?php echo $itemHtml['price4one']; ?></td>
            <?php endif; ?>

            <?php if ($config->get('tmpl_quntity', 1)) : ?>
                <td class="jbcart-quantity"><?php echo $itemHtml['quantityEdit']; ?></td>
            <?php endif; ?>

            <?php if ($config->get('tmpl_subtotal', 1)) : ?>
                <td class="jbcart-subtotal"><?php echo $itemHtml['totalsum']; ?></td>
            <?php endif; ?>

            <td class="jbcart-delete"><span class="jbbutton orange round jsDelete">x</span></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
    <tfoot>

    <?php
    if (!empty($view->items) && !empty($view->modifierPrice)) {
        $this->app->jbassets->less('jbassets:less/cart/modifier.less');
        echo $view->modifierOrderPriceRenderer->render('modifier.default', array(
            'order'   => $view->order,
            'colspan' => $colspan,
        ));
    } ?>

    <tr class="jbcart-row-total">
        <td colspan="<?php echo(($colspan - 3) <= 0 ? 1 : $colspan - 3); ?>" class="jbcart-total-cell">
            <div class="jbcart-items-in-cart">
                <span class="jbcart-label"><?php echo JText::_('JBZOO_CART_TABLE_TOTAL_COUNT'); ?>:</span>
                <span class="jbcart-value jsTotalCount"><?php echo $order->getTotalCount(); ?></span>
            </div>
            <div class="jbcart-price-of-goods">
                <span class="jbcart-label"><?php echo JText::_('JBZOO_CART_TABLE_SUBTOTAL_SUM'); ?>:</span>
                <span class="jbcart-value jsTotalPrice"><?php echo $order->getTotalForItems()->html(); ?></span>
            </div>
        </td>

        <td class="jbcart-shipping-cell">
            <?php if ($view->shipping) : ?>
                <div class="jbcart-label"><?php echo JText::_('JBZOO_CART_TABLE_SHIPPING'); ?>:</div>
                <div class="jbcart-value jsShippingPrice"><?php echo $order->getShippingPrice()->html(); ?></div>
            <?php endif; ?>
        </td>

        <td colspan="<?php echo(($colspan - 3) <= 0 ? 1 : $colspan - 3); ?>" class="jbcart-total-price-cell">
            <div class="jbcart-label"><?php echo JText::_('JBZOO_CART_TABLE_TOTAL_SUM'); ?>:</div>
            <div class="jbcart-value jsTotal"><?php echo $order->getTotalSum()->html(); ?></div>
        </td>
    </tr>

    <tr class="jbcart-row-remove">
        <td colspan="<?php echo $colspan; ?>">
            <span class="jsDeleteAll item-delete-all jbbutton orange">
                <?php echo JText::_('JBZOO_CART_REMOVE_ALL'); ?></span>
        </td>
    </tr>

    </tfoot>
</table>
