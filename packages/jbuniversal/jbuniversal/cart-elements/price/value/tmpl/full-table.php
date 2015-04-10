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
