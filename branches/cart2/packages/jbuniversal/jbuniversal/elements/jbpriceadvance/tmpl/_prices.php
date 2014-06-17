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


if (count($currencyList) > 1) : ?>
    <div class="jbprice-currency-list not-paid-box">
        <?php foreach ($currencyList as $currency) : ?>
            <span class="jbprice-currency jsPriceCurrency jbcurrency jbcurrency-<?php echo strtolower($currency); ?>"
                  data-currency="<?php echo $currency; ?>"
                  title="<?php echo JText::_('JBZOO_JBCURRENCY_' . $currency); ?>"><?php echo $currency; ?></span>
        <?php endforeach; ?>
        <div class="clear clr"></div>
    </div>
<?php endif; ?>

<div class="jbprice-price">
    <?php if ($discount['value'] == 0) : ?>
        <table cellpadding="0" cellspacing="0" border="0" class="no-border">
            <tr>
                <td class="not-paid-box"><span class="jsTotal total"><?php echo $base['total']; ?></span></td>
                <td class="not-paid-box"><?php echo $saleTmpl; ?></td>
                <td><?php echo $newTmpl; ?></td>
                <td><?php echo $hitTmpl; ?></td>
            </tr>
        </table>
    <?php endif; ?>

    <?php if ($discount['value'] > 0) : ?>
        <table cellpadding="0" cellspacing="0" border="0" class="no-border">
            <tr>
                <td class="not-paid-box"><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
                <td class="not-paid-box">
                    <span class="jsPrice price discount-more"><?php echo $base['price']; ?></span><br/>
                    <span class="jsTotal total discount-more"><?php echo $base['total']; ?></span>
                </td>
                <td><?php echo $newTmpl; ?></td>
                <td><?php echo $hitTmpl; ?></td>
            </tr>

            <?php if ($params->get('sale_show', 1) == ElementJBPriceAdvance::SALE_VIEW_TEXT) : ?>
                <tr class="not-paid-box">
                    <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_NOT_SAVE'); ?>:</td>
                    <td><span class="jsSave save discount-more"><?php echo $base['save']; ?></span>
                        (<span class="discount">+<?php echo $discount['format']; ?></span>)
                    </td>
                    <td></td>
                </tr>
            <?php endif; ?>

        </table>
    <?php endif; ?>

    <?php if ($discount['value'] < 0) : ?>
        <table cellpadding="0" cellspacing="0" border="0" class="no-border">
            <tr>
                <td class="not-paid-box"><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
                <td class="not-paid-box">
                    <span class="jsPrice price discount-less"><?php echo $base['price']; ?></span><br/>
                    <span class="jsTotal total discount-less"><?php echo $base['total']; ?></span>
                </td>
                <td class="not-paid-box"><?php echo $saleTmpl; ?></td>
                <td><?php echo $newTmpl; ?></td>
                <td><?php echo $hitTmpl; ?></td>
            </tr>

            <?php if ($params->get('sale_show', 1) == ElementJBPriceAdvance::SALE_VIEW_TEXT) : ?>
                <tr class="not-paid-box">
                    <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_SAVE'); ?>:</td>
                    <td><span class="jsSave save discount-less"><?php echo $base['save']; ?></span>
                        (<span class="discount"><?php echo $discount['format']; ?></span>)
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            <?php endif; ?>

        </table>
    <?php endif; ?>

</div>

<?php if ((int)$params->get('description_show', 0)) : ?>
    <div class="jbprice-description jsDescription"></div>
<?php endif; ?>

<?php if ((int)$params->get('params_tmpl', 1)) : ?>
    <div class="jbprice-selects">
        <?php echo $selects; ?>
        <div class="clr"></div>
    </div>
<?php endif; ?>
