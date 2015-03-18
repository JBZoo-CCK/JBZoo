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


$zoo = App::getInstance('zoo');

$zoo->jbassets->widget('.jsJBZooCartModule', 'JBZoo.CartModule', $basketHelper->getWidgetParams());

$order    = $basketHelper->getOrder();
$items    = $basketHelper->getBasketItems();
$currency = $basketHelper->getCurrency();

?><!--noindex-->
<div class="jbzoo jbcart-module clearfix jsJBZooCartModule" id="jbzooCartModule-'<?php echo $module->id; ?>">

    <?php if (empty($items)) : ?>
        <div class="jbcart-module-empty"><?php echo JText::_('JBZOO_CART_MODULE_EMPTY'); ?></div>
    <?php else: ?>

        <?php if ((int)$params->get('jbcart_items', 1)) : ?>
            <div class="jbcart-module-items">

                <?php foreach ($items as $itemKey => $cartItem) : ?>
                    <div class="<?php echo $itemKey; ?> jsCartItem jbcart-module-item clearfix"
                         data-key="<?php echo $itemKey; ?>">

                        <?php if ((int)$params->get('jbcart_item_delete', 1)) : ?>
                            <span class="jbbutton orange round jsDelete jbcart-item-delete">x</span>
                        <?php endif; ?>

                        <?php if ((int)$params->get('jbcart_item_image', 1)) {
                            echo $cartItem['image'];
                        } ?>

                        <?php echo $cartItem['name']; ?>

                        <?php if ((int)$params->get('jbcart_item_price', 1)) : ?>
                            <div class="jbcart-item-price">
                                <?php echo $cartItem['price4one']; ?>

                                <?php if ((int)$params->get('jbcart_item_quantity', 1)) : ?>
                                    <span class="jbcart-item-price-multiple">x</span>
                                    <?php echo $cartItem['quantity']; ?>
                                <?php endif; ?>

                            </div>

                        <?php elseif ((int)$params->get('jbcart_item_quantity', 1)): ?>
                            <?php echo $cartItem['quantity']; ?>
                        <?php endif; ?>

                        <?php if ((int)$params->get('jbcart_item_total', 1)) {
                            echo $cartItem['totalsum'];
                        } ?>

                        <?php if ((int)$params->get('jbcart_item_params', 1)) {
                            echo $cartItem['params'];
                        } ?>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>

        <?php if ((int)$params->get('jbcart_count_items', 1)) : ?>
            <div class="jbcart-module-line">
                <?php echo JText::_('JBZOO_CART_MODULE_TOTAL_COUNT'); ?>:
                <span class="jbcart-module-total-items">
                    <?php echo $order->getTotalCount() . ' ' . JText::_('JBZOO_CART_COUNT_ABR'); ?>
                </span>
            </div>
        <?php endif ?>


        <?php if ((int)$params->get('jbcart_count_sku', 1)) : ?>
            <div class="jbcart-module-line">
                <?php echo JText::_('JBZOO_CART_MODULE_TOTAL_SKU'); ?>:
                <span class="jbcart-module-total-items">
                    <?php echo $order->getTotalCountSku() . ' ' . JText::_('JBZOO_CART_COUNT_ABR'); ?>
                </span>
            </div>
        <?php endif ?>


        <?php if ((int)$params->get('jbcart_totalsum', 1)) : ?>
            <div class="jbcart-module-line">
                <?php echo JText::_('JBZOO_CART_MODULE_TOTAL_SUM'); ?>:
                <span class="jbcart-module-total-value"><?php echo $order->getTotalSum()->html($currency); ?></span>
            </div>
        <?php endif ?>


        <?php if ((int)$params->get('jbcart_button_empty', 1) && (int)$params->get('jbcart_button_gotocart', 1)) : ?>

            <div class="jbcart-module-buttons clearfix">

                <?php if ((int)$params->get('jbcart_button_empty', 1)): ?>
                    <span class="jbbutton small jbcart-module-empty jsEmptyCart">
                        <?php echo JText::_('JBZOO_CART_MODULE_EMPTY_BUTTON'); ?></span>
                <?php endif ?>

                <?php if ((int)$params->get('jbcart_button_gotocart', 1)): ?>
                    <a rel="nofollow" class="jbbutton green small jbcart-module-gotocart"
                       href="<?php echo $basketHelper->getBasketUrl(); ?>">
                        <?php echo JText::_('JBZOO_CART_MODULE_CART_BUTTON'); ?></a>
                <?php endif ?>

            </div>
        <?php endif ?>

    <?php endif; ?>
</div><!--/noindex-->
