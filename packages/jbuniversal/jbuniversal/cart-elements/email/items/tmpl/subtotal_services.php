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
defined('_JEXEC') or die('Restricted access');

if ($subtotal && $on) :

    if ($payment) : ?>
        <tr>
            <td colspan="2" style="border-bottom: none;"></td>
            <td <?php echo $this->getStyles(); ?>>
                <p style="<?php echo $this->getStyles(); ?>">
                    <strong>Комиссия платежной системы</strong>
                </p>
            </td>
            <td colspan="2" <?php echo $this->getStyles(); ?>>
                <?php echo $order->getPayment()->getName(); ?>
            </td>
            <td <?php echo $this->getStyles(array(
                    'text-align'    => 'right',
                    'border-bottom' => '1px solid #dddddd'
                )
            ); ?>>
                <strong>
                    <?php echo $this->_jbmoney->toFormat($order->getPayment()->getRate(), $currency); ?>
                </strong>
            </td>
        </tr>
    <?php endif;


    if ($shipping) : ?>
        <tr>
            <td colspan="2" style="border-bottom: none;"></td>
            <td <?php echo $this->getStyles(); ?>">
            <p style="<?php echo $this->getStyles(); ?>">
                <strong>Цена доставки</strong>
            </p>
            </td>
            <td colspan="2" <?php echo $this->getStyles(); ?>>
                <?php echo $order->getShipping()->getName(); ?>
            </td>

            <td <?php echo $this->getStyles(array('text-align' => 'right')); ?>>
                <strong>
                    <?php echo $this->_jbmoney->toFormat($order->getShipping()->getRate(), $currency); ?>
                </strong>
            </td>
        </tr>
    <?php endif; ?>

    <tr>
        <td colspan="2" style="border-bottom: none;"></td>
        <td colspan="2" <?php echo $this->getStyles(); ?>>
            <p>
                Промежуточный итог
            </p>
        </td>

        <td colspan="2" <?php echo $this->getStyles(array(
                'text-align'    => 'right',
                'font-size'     => '18px',
                'border-bottom' => '1px solid #dddddd'
            )
        ); ?>
            >
            <em style="color: #dd0055;font-style: italic;">
                <?php echo $order->getTotalForSevices(true); ?>
            </em>
        </td>
    </tr>

<?php endif;