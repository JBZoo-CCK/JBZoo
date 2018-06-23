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

?>

<?php if ($discount->isEmpty()) : ?>

    <table class="jbprice-value-table no-border">
        <tr class="jbprice-value-row">
            <td class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_TOTAL'); ?></td>
            <td class="jbprice-value-total"><?php echo $total->html($currency); ?></td>
        </tr>
    </table>

<?php else: ?>

    <table class="jbprice-value-table no-border">
        <tr class="jbprice-value-row">
            <td class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_PRICE'); ?></td>
            <td class="jbprice-value-price"><?php echo $price->html($currency); ?></td>
        </tr>

        <tr class="jbprice-value-row">
            <td class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_TOTAL'); ?></td>
            <td class="jbprice-value-total"><?php echo $total->html($currency); ?></td>
        </tr>

        <tr class="jbprice-value-row">
            <td class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_SAVE'); ?></td>
            <td class="jbprice-value-save">
                <span class="jbprice-value-save-value"><?php echo $save->html($currency); ?></span>
                <span class="jbprice-value-save-percent">
                    ( <?php echo $save->percent($price)->negative()->text($currency); ?> )
                </span>
            </td>
        </tr>
    </table>

<?php endif; ?>
