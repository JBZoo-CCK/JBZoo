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

?>
<thead>
<tr>
    <th <?php echo $this->getAttrs(array(
            'width' => '30px',
            'align' => 'center')
    );?>
        >
        #
    </th>

    <th <?php echo $this->getAttrs(array(
            'width' => '15%')
    );?>
        >
        <?php echo JText::_('JBZOO_ORDER_ITEM_IMAGE'); ?>
    </th>

    <th <?php echo $this->getAttrs(array(
            'width' => '28%')
    );?>
        >
        <?php echo JText::_('JBZOO_ORDER_ITEM_NAME'); ?>
    </th>

    <th <?php echo $this->getAttrs(array(
            'width' => '18%')
    );?>
        >
        <?php echo JText::_('JBZOO_ORDER_PRICE_PER_PIECE'); ?>
    </th>

    <th <?php echo $this->getAttrs(array(
            'width' => '15%',
            'align' => 'center')
    );?>
        >
        <?php echo JText::_('JBZOO_ORDER_ITEM_QUANTITY'); ?>
    </th>

    <th <?php echo $this->getAttrs(array(
            'width' => '15%',
            'align' => 'center')
    );?>
        >
        <?php echo JText::_('JBZOO_ORDER_ITEM_COST'); ?>
    </th>
</tr>
</thead>