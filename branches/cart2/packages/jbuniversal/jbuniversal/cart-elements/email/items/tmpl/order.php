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

if (!empty($items)) : ?>

    <h2 <?php $this->getStyles(array(
            'border-bottom' => '1px solid #dddddd',
            'padding'       => '0 0 12px 0',
            'margin'        => '0 0 20px 0')
    ); ?>
        >
        <?php echo $title; ?>
    </h2>

    <table <?php echo $this->getAttrs(array(
            'cellspacing' => 0,
            'cellpadding' => 8,
            'width'       => '100%'
        )) .
        $this->getStyles(array(
            'border-collapse' => 'collapse'
        )); ?>
        >

        <!-- table_head.php -->
        <?php echo $this->partial('table_head'); ?>

        <!-- table_body.php -->
        <?php echo $this->partial('table_body', array(
                'items'    => $items,
                'currency' => $this->currency()
            )
        ); ?>

        <!--
             - table_foot.php
              -- subtotal_services.php
              -- subtotal_modifiers.php
         -->
        <?php echo $this->partial('table_foot', array(
                'order'    => $order,
                'currency' => $currency,
                'subtotal' => (int)$this->config->get('subtotal', 1)
            )
        ); ?>

    </table>
<?php endif;