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
    <div class="jbprice-price">
        <table cellpadding="0" cellspacing="0" border="0" class="no-border">
            <tr>
                <td><?php echo JText::_('JBZOO_JBPRICE_PRICE_TOTAL'); ?>:</td>
                <td><span class="jsTotal total"><?php echo $prices['total']->html(); ?></span></td>
            </tr>
        </table>
    </div>
</div>
