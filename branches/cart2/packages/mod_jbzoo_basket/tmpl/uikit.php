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

$order = $basketHelper->getOrder();
$items = $basketHelper->getBasketItems();

?><!--noindex-->
<div class="jbzoo jbcart-module clearfix jsJBZooCartModule" id="jbzooCartModule-'<?php echo $module->id; ?>">

    <?php if (empty($items)) : ?>
        <div class="jbcart-module-empty"><?php echo JText::_('JBZOO_CART_MODULE_EMPTY'); ?></div>
    <?php else: ?>

        <div class="jbcart-module-items">
            <?php foreach ($items as $itemKey => $cartItem) : ?>
                <div class="<?php echo $itemKey; ?> jsCartItem jbcart-module-item clearfix" data-key="<?php echo $itemKey; ?>">
                    <span class="uk-button uk-button-danger uk-button-small round jsDelete jbcart-item-delete">
                        <i class="uk-icon-trash-o"></i>
                    </span>

                    <?php echo $cartItem['image']; ?>
                    <?php echo $cartItem['name']; ?>

                    <div class="jbcart-item-price">
                        <?php echo $cartItem['price4one']; ?>
                        <span class="jbcart-item-price-multiple">x</span>
                        <?php echo $cartItem['quantity']; ?>
                    </div>

                    <?php echo $cartItem['params']; ?>
                </div>
            <?php endforeach; ?>
        </div>


        <?php if ((int)$params->get('items_show', 1)) : ?>
            <div class="jbcart-module-line">
                <?php echo JText::_('JBZOO_CART_MODULE_TOTAL_COUNT'); ?>:
                <span class="jbcart-module-total-items">
                    <?php echo $order->getTotalCount() . ' ' . JText::_('JBZOO_CART_COUNT_ABR'); ?>
                </span>
            </div>
        <?php endif ?>


        <?php if ((int)$params->get('lots_show', 1)) : ?>
            <div class="jbcart-module-line">
                <?php echo JText::_('JBZOO_CART_MODULE_TOTAL_SKU'); ?>:
                <span class="jbcart-module-total-items">
                    <?php echo $order->getTotalCountSku() . ' ' . JText::_('JBZOO_CART_COUNT_ABR'); ?>
                </span>
            </div>
        <?php endif ?>


        <?php if ((int)$params->get('summa_show', 1)) : ?>
            <div class="jbcart-module-line">
                <?php echo JText::_('JBZOO_CART_MODULE_TOTAL_SUM'); ?>:
                <span class="jbcart-module-total-value"><?php echo $order->getTotalSum()->html(); ?></span>
            </div>
        <?php endif ?>


        <?php if ((int)$params->get('cancel_show', 1) && (int)$params->get('link_show', 1)) : ?>

            <div class="jbcart-module-buttons">

                <?php if ((int)$params->get('cancel_show', 1)): ?>
                    <span class="uk-button uk-button-danger jbcart-module-empty jsEmptyCart">
                        <i class="uk-icon-trash-o"></i>
                        <?php echo JText::_('JBZOO_CART_MODULE_EMPTY_BUTTON'); ?>
                    </span>
                <?php endif ?>

                <?php if ((int)$params->get('link_show', 1)): ?>
                    <a rel="nofollow" class="uk-button uk-button-success jbcart-module-gotocart"
                       href="<?php echo $basketHelper->getBasketUrl(); ?>">
                        <?php echo JText::_('JBZOO_CART_MODULE_CART_BUTTON'); ?>
                    </a>
                <?php endif ?>

            </div>
        <?php endif ?>

    <?php endif; ?>
</div><!--/noindex-->