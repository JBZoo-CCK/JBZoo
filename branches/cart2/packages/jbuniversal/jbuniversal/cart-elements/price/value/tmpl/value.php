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
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="jbPriceElementValue">

    <?php if ($total->isEmpty() && isset($message{1})) {
        echo $message;

    } elseif ($mode == JBCartElementPriceValue::PRICE_VIEW_FULL) { ?>
        <div class="jbprice-price">

            <?php if ($discount->isEmpty()) : ?>
                <table cellpadding="0" cellspacing="0" border="0" class="no-border">
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
                        <td><span class="jsTotal total"><?php echo $total->html($currency); ?></span></td>
                    </tr>
                </table>
            <?php endif;

            if ($discount->isPositive()) : ?>
                <table cellpadding="0" cellspacing="0" border="0" class="no-border">

                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_PRICE'); ?>:</td>
                        <td>
                            <span class="jsPrice price discount-less">
                                <?php echo $price->html($currency); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('JBPRICE'); ?>:</td>
                        <td>
                            <span class="jsTotal total discount-less">
                                <?php echo $total->html($currency); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_SAVE'); ?>:</td>
                        <td>
                            <span class="save discount-less">
                                <span class="jsSave"><?php echo $save->html($currency); ?></span>
                                (<span class="discount"><?php echo $save->percent($price)->text($currency); ?></span>)
                            </span>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>

        </div>
    <?php } elseif ($mode == JBCartElementPriceValue::PRICE_VIEW_PRICE) { ?>
        <span class="jsPrice price"><?php echo $price->html($currency); ?></span>
    <?php } elseif ($mode == JBCartElementPriceValue::PRICE_VIEW_TOTAL) { ?>
        <span class="jbprice-price">
            <span class="jsTotal total">
                <?php echo $total->html($currency); ?>
            </span>
        </span>
    <?php } elseif ($mode == JBCartElementPriceValue::PRICE_VIEW_DISCOUNT && !$discount->isEmpty()) {

        if ($discount->isPositive() > 0) : ?>
            <span class="price">+<?php echo $discount->html($currency); ?></span>
        <?php else : ?>
            <span class="price">+<?php echo $discount->html($currency); ?></span>
        <?php endif;
    } elseif ($mode == JBCartElementPriceValue::PRICE_VIEW_SAVE && $save->isPositive()) { ?>
        <span class="jsSave discount">
            <?php echo $save->html($currency); ?>
        </span>
    <?php } ?>
</div>
