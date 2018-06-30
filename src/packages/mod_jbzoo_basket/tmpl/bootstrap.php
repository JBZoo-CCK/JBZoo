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

$application = $modHelper->app->zoo->getApplication();
$appTemplate = $application->params->get('template', 'bootstrap');

if ($appTemplate !== 'bootstrap') {
    $modHelper->app->jbtemplate->regHelpersByTpl('bootstrap');
}

$bootstrap = $modHelper->app->jbbootstrap;

$cart     = JBCart::getInstance();
$order    = $modHelper->getOrder();
$currency = $modHelper->getCurrency();
$items    = $modHelper->getBasketItems(array(
    'class' => array(
        'image' => 'thumbnail'
    )
));
?>
<div class="jbzoo jbcart-module jsJBZooCartModule" id="<?php echo $modHelper->getModuleId(); ?>">

    <?php if (empty($items)) : ?>
        <div class="jbcart-module-empty clearfix"><?php echo JText::_('JBZOO_CART_MODULE_EMPTY'); ?></div>
    <?php else : ?>

        <?php if ((int)$params->get('jbcart_items', 1)) : ?>
            <div class="jbcart-module-items">

                <?php foreach ($items as $itemKey => $cartItem) :
                    $attrs = array(
                        'data-key'     => $itemKey,
                        'data-jbprice' => $cart->get($itemKey . '.element_id') . '-' . $cart->get($itemKey . '.item_id'),
                        'class'        => array(
                            $itemKey,
                            'jsCartItem',
                            'jbcart-module-item',
                            'clearfix'
                        ),
                    );
                    ?>

                    <div <?php echo $modHelper->attrs($attrs);?>>

                        <?php if ((int)$params->get('jbcart_item_delete', 1)) : ?>
                            <span class="btn btn-danger btn-xs btn-mini round jsDelete jbcart-item-delete">
                                <?php echo $bootstrap->icon('remove', array('type' => 'white')); ?>
                            </span>
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


        <?php if ((int)$params->get('jbcart_button_empty', 1) || (int)$params->get('jbcart_button_gotocart', 1)) : ?>

            <div class="jbcart-module-buttons clearfix">

                <?php if ((int)$params->get('jbcart_button_empty', 1)): ?>
                    <span class="btn btn-danger jbcart-module-empty jsEmptyCart">
                        <?php echo $bootstrap->icon('trash', array('type' => 'white')); ?>
                        <?php echo JText::_('JBZOO_CART_MODULE_EMPTY_BUTTON'); ?>
                    </span>
                <?php endif ?>

                <?php if ((int)$params->get('jbcart_button_gotocart', 1)): ?>
                    <a rel="nofollow" class="btn btn-success jbcart-module-gotocart"
                       href="<?php echo $modHelper->getBasketUrl(); ?>">
                        <?php echo $bootstrap->icon('shopping-cart', array('type' => 'white')); ?>
                        <?php echo JText::_('JBZOO_CART_MODULE_CART_BUTTON'); ?>
                    </a>
                <?php endif ?>

            </div>
        <?php endif ?>

    <?php endif; ?>

</div>
