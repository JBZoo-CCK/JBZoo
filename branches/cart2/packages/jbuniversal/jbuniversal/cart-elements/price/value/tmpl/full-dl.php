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

    <dl class="jbprice-value jbprice-value-dl no-border">
        <dt class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_TOTAL'); ?></dt>
        <dd class="jbprice-value-total"><?php echo $total->html($currency); ?></dd>
    </dl>

<?php elseif ($discount->isPositive()) : ?>

    <dl class="jbprice-value jbprice-value-dl no-border">
        <dt class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_PRICE'); ?></dt>
        <dd class="jbprice-value-price"><?php echo $price->html($currency); ?></dd>

        <dt class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_TOTAL'); ?></dt>
        <dd class="jbprice-value-total"><?php echo $total->html($currency); ?></dd>

        <dt class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_SAVE'); ?></dt>
        <dd class="jbprice-value-save">
            <span class="jbprice-value-save-value"><?php echo $save->html($currency); ?></span>
            <span class="jbprice-value-save-percent">
                ( <?php echo $save->percent($price)->text($currency); ?> )
            </span>
        </dd>
    </dl>

<?php endif; ?>
