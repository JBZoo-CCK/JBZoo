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

if (!empty($fields)) : ?>

    <dt>
        <?php echo JText::_('JBZOO_ORDER_SHIPPING_NAME'); ?>
    </dt>
    <dd>
        <p><?php echo $this->getName(); ?></p>
    </dd>

    <dt>
        <?php echo JText::_('JBZOO_ORDER_SHIPPING_PRICE'); ?>
    </dt>
    <dd>
        <p><?php echo $this->_jbmoney->toFormat($this->get('value', 0), $this->currency()); ?></p>
    </dd>

    <?php foreach ($fields as $key => $field) : ?>
        <dt>
            <?php echo JText::_('JBZOO_ORDER_SHIPPING_NEWPOST_' . strtoupper($key)); ?>
        </dt>

        <dd>
            <p><?php echo $field; ?></p>
        </dd>

    <?php endforeach; ?>

    <dt>
        <?php echo JText::_('JBZOO_ORDER_SHIPPING_STATUS'); ?>
    </dt>
    <dd>
        <p>
            <select name="shipping_status" style="width: 180px;">
                <option>В процессе</option>
            </select>
        </p>
    </dd>

<?php endif;