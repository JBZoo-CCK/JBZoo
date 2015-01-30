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

if ($subtotal && $on) :

    if ($payment && !$payment->getRate()->isEmpty()) : ?>
        <tr>
            <td colspan="2" style="border-bottom: none;"></td>

            <td <?php echo $this->getStyles(); ?>>
                <p>
                    <strong>Комиссия платежной системы</strong>
                </p>
            </td>

            <td colspan="2" <?php echo $this->getStyles(); ?>>
                <?php echo $order->getPayment()->getName(); ?>
            </td>

            <td <?php echo $this->getStyles(array(
                'text-align'    => 'right',
                'border-bottom' => '1px solid #dddddd'
            )); ?>>
                <strong><?php echo $order->val($payment->getRate(), $currency); ?></strong>
            </td>
        </tr>
    <?php endif;

    if ($shipping && !$shipping->getRate()->isEmpty()) : ?>
        <tr>
            <td colspan="2" style="border-bottom: none;"></td>

            <td <?php echo $this->getStyles(); ?>>
                <p>
                    <strong>Цена доставки</strong>
                </p>
            </td>

            <td colspan="2" <?php echo $this->getStyles(); ?>>
                <?php echo $shipping->getName(); ?>
            </td>

            <td <?php echo $this->getStyles(array(
                'text-align'    => 'right',
                'border-bottom' => '1px solid #dddddd'
            )); ?>>
                <strong><?php echo $order->val($shipping->getRate(), $currency); ?></strong>
            </td>
        </tr>
    <?php endif;
endif;