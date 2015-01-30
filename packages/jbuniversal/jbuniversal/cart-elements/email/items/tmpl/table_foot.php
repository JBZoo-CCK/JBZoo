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
defined('_JEXEC') or die('Restricted access'); ?>
<tfoot>

<!-- Subtotal price for items -->
<?php if ($subtotal && (int)$this->config->get('subtotal_items', 1)) : ?>
    <tr>
        <td <?php echo $this->getAttrs(array(
                'colspan' => 2,
                'border'  => 0
            )) .
            $this->getStyles(array(
                'padding' => '8px 0 8px 8px'
            )); ?>></td>

        <td <?php echo $this->getAttrs(array('colspan' => 2)) . $this->getStyles(); ?>>
            <p>Промежуточный итог</p>
        </td>

        <td <?php echo $this->getAttrs(array(
                'align'   => 'right',
                'colspan' => 2
            )) .
            $this->getStyles(array(
                'text-align' => 'right',
                'padding'    => '8px 0 8px 8px'
            ), true); ?>>
            <?php echo $this->fontColor($order->getTotalForItems(), '#dd0055', 4); ?>
        </td>
    </tr>
<?php endif;

echo $this->partial('subtotal_services', array(
        'order'    => $order,
        'shipping' => $order->getShipping(),
        'payment'  => $order->getPayment(),
        'currency' => $currency,
        'subtotal' => $subtotal,
        'on'       => (int)$this->config->get('subtotal_services', 1)
    )
);

echo $this->partial('subtotal_modifiers', array(
        'order'     => $order,
        'modifiers' => $order->getModifiersOrderPrice(),
        'subtotal'  => $subtotal,
        'on'        => (int)$this->config->get('subtotal_modifiers', 1)
    )
); ?>

<tr>
    <td <?php echo $this->getAttrs(array('colspan' => 2, 'border' => 0)); ?>></td>
    <td <?php echo $this->getAttrs(array('colspan' => 2, 'border' => 0)); ?>>
        <strong>Итого к оплате</strong>
    </td>
    <td <?php echo $this->getAttrs(array(
            'colspan' => 2,
            'border'  => 0,
            'align'   => 'right'
        )) . ' ' .
        $this->getStyles(array('padding' => '8px 0 8px 8px')); ?>>
        <strong><?php echo $this->fontColor($order->getTotalSum(true), '#a00', 5); ?></strong>
    </td>
</tr>

</tfoot>
