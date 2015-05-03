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

if (!$payment) {
    return;
}

$this->sum->addModify($payment); ?>
<tr>
    <td class="noborder-btm"></td>
    <th>
        <p><?php echo JText::_('JBZOO_ORDER_PAYMENT_FEE'); ?></p>
    </th>
    <td>
        <?php echo $payment->getName(); ?>
        <em>(
            <?php
            if ($payment->isModify()) {
                echo JText::_('JBZOO_ORDER_PAYMENT_INCLUDED');
            } else {
                echo JText::_('JBZOO_ORDER_PAYMENT_NOT_INCLUDED');
            }
            ?>
        )</em>
    </td>
    <td class="align-right">
        <?php echo $payment->getRate()->htmlAdv($currency, true); ?>
    </td>
    <td class="align-right">
        <?php echo $this->sum->html(); ?>
    </td>
</tr>