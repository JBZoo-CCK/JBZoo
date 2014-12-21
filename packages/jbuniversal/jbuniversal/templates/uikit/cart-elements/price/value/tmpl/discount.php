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
defined('_JEXEC') or die('Restricted access');
if ($discount->isPositive()) : ?>
    <table cellpadding="0" cellspacing="0" border="0" class="uk-table table-no-border">
        <tr>
            <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_PRICE'); ?>:</td>
            <td>
                <span class="jsPrice price discount-less"><?php echo $prices['price']->html(); ?></span>
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
            <td>
                <span class="jsTotal total discount-less"><?php echo $prices['total']->html(); ?></span>
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_SAVE'); ?>:</td>
            <td>
                <span class="save discount-less">
                    <span class="jsSave"><?php echo $prices['save']->html(); ?></span>
                    (<span class="discount"><?php echo $discount->noStyle(); ?></span>)
                </span>
            </td>
        </tr>
    </table>
<?php endif; ?>