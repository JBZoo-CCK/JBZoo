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


if (!$this->config->get('total', 1)) {
    return;
}
?>

<tr>
    <td <?php echo $this->getAttrs(array('colspan' => 2, 'border' => 0)); ?>></td>

    <td <?php echo $this->getAttrs(array('colspan' => 3, 'border' => 0)); ?>>
        <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_TOTAL_TITLE'); ?></strong>
    </td>

    <td <?php echo $this->getAttrs(array('border' => 0)) .
        $this->getStyles(array('padding' => '8px 0 8px 8px')); ?>>

        <strong><?php echo $this->fontColor($order->getTotalSum()->html($this->_getCurrency()), '#dd0055', 5); ?></strong>
    </td>
</tr>