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


$uniqid = uniqid('jsJBPriceAdvance-');

$mode = (int)$params->get('only_price_mode', 1);
?>

<div class="jsJBPriceAdvance jbprice-advance" id="<?php echo $uniqid; ?>">
    <?php if ($mode == ElementJBPriceAdvance::PRICE_VIEW_FULL) : ?>
        <div class="jbprice-price">
            <?php if ($discount['value'] == 0) : ?>
                <span class="total"><?php echo $base['total']; ?></span>
            <?php endif; ?>

            <?php if ($discount['value'] > 0) : ?>

                <table cellpadding="0" cellspacing="0" border="0" class="no-border">
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_PRICE'); ?>:</td>
                        <td><span class="jsPrice price discount-more"><?php echo $base['price']; ?></span></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
                        <td><span class="jsTotal total discount-more"><?php echo $base['total']; ?></span></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_NOT_SAVE'); ?>:</td>
                        <td><span class="jsSave save discount-more"><?php echo $base['save']; ?></span>
                            (<span class="discount">+<?php echo $discount['format']; ?></span>)
                        </td>
                    </tr>
                </table>

            <?php endif; ?>

            <?php if ($discount['value'] < 0) : ?>

                <table cellpadding="0" cellspacing="0" border="0" class="no-border">
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_PRICE'); ?>:</td>
                        <td><span class="jsPrice price discount-less"><?php echo $base['price']; ?></span></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
                        <td><span class="jsTotal total discount-less"><?php echo $base['total']; ?></span></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_SAVE'); ?>:</td>
                        <td><span class="save discount-less"><span class="jsSave"><?php echo $base['save']; ?></span>
                                (<span class="discount"><?php echo $discount['format']; ?></span>)</span>
                        </td>
                    </tr>
                </table>

            <?php endif; ?>

        </div>
    <?php endif; ?>

    <?php if ($mode == ElementJBPriceAdvance::PRICE_VIEW_PRICE) : ?>
        <span class="price"><?php echo $base['price']; ?></span>
    <?php endif; ?>

    <?php if ($mode == ElementJBPriceAdvance::PRICE_VIEW_TOTAL) : ?>
        <span class="price"><?php echo $base['total']; ?></span>
    <?php endif; ?>

    <?php if ($mode == ElementJBPriceAdvance::PRICE_VIEW_DISCOUNT && $discount['value']) :?>

        <?php if ($discount['value'] > 0) :?>
            <span class="price">+<?php echo $discount['format']; ?></span>
        <?php else :?>
            <span class="price">+<?php echo $discount['format']; ?></span>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($mode == ElementJBPriceAdvance::PRICE_VIEW_SAVE && $base['save']) : ?>
        <span class="discount"><?php echo $base['save']; ?></span>
    <?php endif; ?>

</div>
