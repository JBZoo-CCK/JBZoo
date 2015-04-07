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

    <div class="jbprice-value jbprice-value-div no-border">
        <div>
            <span class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_TOTAL'); ?></span>
            <span class="jbprice-value-total"><?php echo $total->html($currency); ?></span>
        </div>
    </div>

<?php elseif ($discount->isPositive()) : ?>

    <div class="jbprice-value jbprice-value-div no-border">
        <div>
            <span class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_PRICE'); ?></span>
            <span class="jbprice-value-price"><?php echo $price->html($currency); ?></span>
        </div>
        <div>
            <span class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_TOTAL'); ?></span>
            <span class="jbprice-value-total"><?php echo $total->html($currency); ?></span>
        </div>
        <div>
            <span class="jbprice-value-label"><?php echo JText::_('JBZOO_ELEMENT_PRICE_VALUE_LABEL_SAVE'); ?></span>
            <span class="jbprice-value-save">
                <span class="jbprice-value-save-value"><?php echo $save->html($currency); ?></span>
                <span class="jbprice-value-save-percent">
                    ( <?php echo $save->percent($price)->text($currency); ?> )
                </span>
            </span>
        </div>
    </div>

<?php endif; ?>
