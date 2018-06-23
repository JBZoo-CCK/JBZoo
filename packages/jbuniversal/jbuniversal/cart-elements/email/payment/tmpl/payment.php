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