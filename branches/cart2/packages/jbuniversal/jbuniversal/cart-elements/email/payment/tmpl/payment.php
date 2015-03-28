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


$payment = $order->getPayment();

?>

<table <?php echo $this->getAttrs(array(
    'width'       => '100%',
    'cellpadding' => 8
)); ?>>

    <tr>
        <td style="width:30%;">
            <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_PAYMENT_METHOD'); ?></strong>
        </td>

        <td><?php echo $payment->getName(); ?></td>
    </tr>

    <tr>
        <td align="left">
            <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_PAYMENT_COMMISSION'); ?></strong>
        </td>
        <td align="left">
            <?php echo $payment->getRate()->html(); ?>
        </td>
    </tr>

    <tr>
        <td align="left">
            <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_PAYMENT_STATUS'); ?></strong>
        </td>
        <td align="left">
            <?php echo $payment->getStatus()->getName(); ?>
        </td>
    </tr>
</table>