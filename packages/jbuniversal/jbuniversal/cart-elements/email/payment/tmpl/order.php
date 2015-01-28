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
<table <?php echo $this->getAttrs(array(
        'width'       => '100%',
        'cellpadding' => 10,
        'bgcolor'     => '#fafafa',
        'frame'       => 'box'
    )) .
    $this->getStyles(array(
        'border'        => '1px solid #dddddd',
        'border-radius' => '4px',
        'margin-top'    => '35px'
    ));?>
    >
    <tr>
        <td>
            <h3 style="color: #444444;margin: 0 0 15px 0;font-size: 18px;">
                <?php echo $title; ?>
            </h3>
        </td>
    </tr>

    <tr>
        <td>
        <strong>
            <?php echo JText::_('JBZOO_EMAIL_PAYMENT_METHOD'); ?>
        </strong>
        </td>

        <td>
            <?php echo $payment->getName(); ?>
        </td>
    </tr>

    <tr>
        <td align="left">
            <strong>
                <?php echo JText::_('JBZOO_EMAIL_PAYMENT_COMMISSION'); ?>
            </strong>
        </td>
        <td align="left">
            <?php echo $payment->getRate()->html(); ?>
        </td>
    </tr>

    <tr>
        <td align="left">
            <strong>
                <?php echo JText::_('JBZOO_EMAIL_PAYMENT_STATUS'); ?>
            </strong>
        </td>
        <td align="left">
            <?php echo $payment->getStatus(); ?>
        </td>
    </tr>
</table>