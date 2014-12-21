<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="jbPriceElementValue">

    <?php if ($mode == JBCartElementPriceValue::PRICE_VIEW_FULL) : ?>
        <div class="jbprice-price">
            <?php if ($discount->isEmpty()) : ?>

                <table cellpadding="0" cellspacing="0" border="0" class="uk-table table-no-border">
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
                        <td><span class="jsTotal total"><?php echo $prices['total']->html($currency); ?></span></td>
                    </tr>
                </table>

            <?php endif;

            if ($discount->isPositive()) : ?>

                <table cellpadding="0" cellspacing="0" border="0" class="uk-table table-no-border">
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_PRICE'); ?>:</td>
                        <td>
                            <span class="jsPrice price discount-less">
                                <?php echo $prices['price']->html($currency); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
                        <td>
                            <span class="jsTotal total discount-less">
                                <?php echo $prices['total']->html($currency); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_SAVE'); ?>:</td>
                        <td>
                            <span class="save discount-less">
                                <span class="jsSave"><?php echo $prices['save']->html($currency); ?></span>
                                (<span class="discount"><?php echo $discount->html($currency); ?></span>)
                            </span>
                        </td>
                    </tr>
                </table>

            <?php endif; ?>
        </div>
    <?php endif;

    if ($mode == JBCartElementPriceValue::PRICE_VIEW_PRICE) : ?>
        <span class="jsPrice price"><?php echo $prices['price']->html($currency); ?></span>
    <?php endif;

    if ($mode == JBCartElementPriceValue::PRICE_VIEW_TOTAL) : ?>
        <span class="jbprice-price">
            <span class="jsTotal total">
                <?php echo $prices['total']->html($currency); ?>
            </span>
        </span>
    <?php endif;

    if ($mode == JBCartElementPriceValue::PRICE_VIEW_DISCOUNT && !$discount->isEmpty()) :

        if ($discount->isPositive() > 0) : ?>
            <span class="price">+<?php echo $discount->html($currency); ?></span>
        <?php else : ?>
            <span class="price">+<?php echo $discount->html($currency); ?></span>
        <?php endif;
    endif;

    if ($mode == JBCartElementPriceValue::PRICE_VIEW_SAVE && $prices['save']->isPositive()) : ?>
        <span class="jsSave discount">
            <?php echo $prices['save']->html($currency); ?>
        </span>
    <?php endif; ?>
</div>
