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


$config = $this->config;
?>
<thead>
<tr>
    <th <?php echo $this->getAttrs(array('align' => 'left')); ?>>
        <?php echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_ID');?>
    </th>

    <th <?php echo $this->getAttrs(array('align' => 'left')); ?>>
        <?php if ($config->get('tmpl_image_show', 1)) {
            echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_IMAGE');
        } ?>
    </th>

    <th <?php echo $this->getAttrs(array('align' => 'left')); ?>>
        <?php echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_ITEMNAME'); ?>
    </th>

    <th <?php echo $this->getAttrs(array('align' => 'left')); ?>>
        <?php if ($config->get('tmpl_price4one', 1)) {
            echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_PRICE');
        } ?>
    </th>

    <th <?php echo $this->getAttrs(array('align' => 'left')); ?>>
        <?php if ($config->get('tmpl_quntity', 1)) {
            echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_QUANTITY');
        } ?>
    </th>

    <th <?php echo $this->getAttrs(array('align' => 'left')); ?>>
        <?php if ($config->get('tmpl_subtotal', 1)) {
            echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_ITEMTOTAL');
        } ?>
    </th>

</tr>
</thead>

