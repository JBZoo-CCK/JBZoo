<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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