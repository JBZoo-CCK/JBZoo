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

if (!$shipping) {
    return;
}

$this->sum->addModify($shipping); ?>
<tr>
    <td class="noborder-btm"></td>
    <th>
        <p><?php echo JText::_('JBZOO_ORDER_SHIPPING_FEE'); ?></p>
    </th>
    <td>
        <?php echo $shipping->getName(); ?>
        <em>(
            <?php
            if ($shipping->isModify()) {
                echo JText::_('JBZOO_ORDER_SHIPPING_INCLUDED');
            } else {
                echo JText::_('JBZOO_ORDER_SHIPPING_NOT_INCLUDED');
            }
            ?>
        )</em>
    </td>
    <td class="align-right">
        <?php echo $shipping->getRate()->htmlAdv($currency, true); ?>
    </td>
    <td class="align-right">
        <?php echo $this->sum->html(); ?>
    </td>
</tr>