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

if (!empty($data)) : ?>

    <tr>
        <td <?php echo $this->getAttrs(array(
            'colspan' => 2
        )); ?>
            >
            <h3 style="color: #444444;font-size: 18px;">
                <?php echo $title; ?>
            </h3>
        </td>
    </tr>

    <?php foreach ($data as $key => $field) : ?>

        <?php if (empty($field)) :
            continue;
        endif; ?>
        <tr>
            <td align="left">
                <strong>
                    <?php echo JText::_('JBZOO_ORDER_SHIPPINGFIELDS_' . strtoupper($key)); ?>
                </strong>
            </td>

            <td align="left">
                <?php echo $field->get('value'); ?>
            </td>

        </tr>
    <?php endforeach;
endif;