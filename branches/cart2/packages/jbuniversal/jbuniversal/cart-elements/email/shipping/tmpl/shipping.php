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


$shipping = $this->getOrder()->getShipping();

?>
<table <?php echo $this->getAttrs(array(
    'width'       => '100%',
    'cellpadding' => 8
)); ?>>

    <tr>
        <td align="left" style="width: 30%;">
            <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_SHIPPING_METHOD'); ?></strong>
        </td>

        <td align="left">
            <?php echo $shipping->getName(); ?>
        </td>
    </tr>

    <tr>
        <td align="left">
            <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_SHIPPING_PRICE'); ?></strong>
        </td>

        <td align="left">
            <?php echo $shipping->getRate(); ?>
        </td>
    </tr>

    <tr>
        <td align="left">
            <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_SHIPPING_STATUS'); ?></strong>
        </td>

        <td align="left">
            <?php echo $shipping->getStatus()->getName(); ?>
        </td>
    </tr>

    <tr>
        <td align="left" valign="top">
            <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_SHIPPING_INFO'); ?></strong>
        </td>

        <td align="left">
            <?php
            $shippingRender = $this->app->jbrenderer->create('Shipping');
            echo $shippingRender->renderAdminEdit(array('order' => $order, 'style' => 'none'));

            if ($this->config->get('fields', 1)) {
                $shippingFieldsRender = $this->app->jbrenderer->create('ShippingFields');
                echo $shippingFieldsRender->renderAdminEdit(array('order' => $order));
            }
            ?>
        </td>
    </tr>

</table>
