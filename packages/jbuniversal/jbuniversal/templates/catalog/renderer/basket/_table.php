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

$this->app->jbassets->less('jbassets:less/cart/table.less');

$string = $this->app->jbstring;
$jbhtml = $this->app->jbhtml;

$cart   = JBCart::getInstance();
$order  = $cart->newOrder();
$config = $cart->getConfig();

?>

<table class="jbcart-table jsJBZooCartTable">
    <thead>
    <tr>
        <th class="jbcart-col jbcart-col-image"></th>
        <th class="jbcart-col jbcart-col-name"><?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?></th>
        <th class="jbcart-col jbcart-col-price"><?php if ($config->get('tmpl_price4one', 1)) {
                echo JText::_('JBZOO_CART_ITEM_PRICE');
            } ?></th>

        <th class="jbcart-col jbcart-col-quantity"><?php if ($config->get('tmpl_quntity', 1)) {
                echo JText::_('JBZOO_CART_ITEM_QUANTITY');
            } ?></th>

        <th class="jbcart-col jbcart-col-subtotal"><?php if ($config->get('tmpl_subtotal', 1)) {
                echo JText::_('JBZOO_CART_ITEM_SUBTOTAL');
            } ?></th>

        <th class="jbcart-col jbcart-col-delete"></th>
    </tr>
    </thead>
    <tbody>

    <tr class="jbcart-row-empty">
        <td colspan="6" class="jbcart-cell-empty"></td>
    </tr>

    <?php
    foreach ($view->itemsHtml as $itemKey => $itemHtml) : ?>
        <tr class="jbcart-row jsCartTableRow js<?php echo $itemKey; ?>" data-key="<?php echo $itemKey; ?>">
            <td class="jbcart-image">
                <?php if ($config->get('tmpl_image_show', 1)) {
                    echo $itemHtml['image'];
                } ?>
            </td>
            <td class="jbcart-name">
                <?php echo $itemHtml['name']; ?>
                <?php if ($config->get('tmpl_sku_show', 1)) {
                    echo $itemHtml['sku'];
                } ?>
                <?php echo $itemHtml['params']; ?>
            </td>
            <td class="jbcart-price"><?php
                if ($config->get('tmpl_price4one', 1)) {
                    echo $itemHtml['price4one'];
                } ?>
            </td>
            <td class="jbcart-quantity"><?php
                if ($config->get('tmpl_quntity', 1)) {
                    echo $itemHtml['quantityEdit'];
                } ?>
            </td>
            <td class="jbcart-subtotal">
                <?php if ($config->get('tmpl_subtotal', 1)) {
                    echo $itemHtml['totalsum'];
                } ?>
            </td>
            <td class="jbcart-delete"><span class="jbbutton orange round jsDelete">x</span></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
    <tfoot>

    <?php
    if (!empty($view->items) && !empty($view->modifierPrice)) {
        $this->app->jbassets->less('jbassets:less/cart/modifier.less');
        echo $view->modifierOrderPriceRenderer->render('modifier.default', array(
            'order' => $view->order,
        ));
    } ?>

    <tr class="jbcart-row-total">
        <td colspan="3" class="jbcart-total-cell">
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

        <td colspan="2" class="jbcart-total-price-cell">
            <div class="jbcart-label"><?php echo JText::_('JBZOO_CART_TABLE_TOTAL_SUM'); ?>:</div>
            <div class="jbcart-value jsTotal"><?php echo $order->getTotalSum()->html(); ?></div>
        </td>
    </tr>

    <tr class="jbcart-row-remove">
        <td colspan="6">
            <span class="jsDeleteAll item-delete-all jbbutton orange">
                <?php echo JText::_('JBZOO_CART_REMOVE_ALL'); ?></span>
        </td>
    </tr>

    </tfoot>
</table>
