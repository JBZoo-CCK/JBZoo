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

if (!$this->config->get('subtotal', 1)) {
    return;
}
?>

<?php if ((int)$this->config->get('subtotal_items', 1)) : ?>
    <tr>
        <td <?php echo $this->getAttrs(array('colspan' => 2, 'border' => 0)) .
            $this->getStyles(array('padding' => '8px 0 8px 8px')); ?>></td>

        <td <?php echo $this->getAttrs(array('colspan' => 3)) . $this->getStyles(); ?>>
            <p><?php echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_SUBTOTAL_TITLE'); ?></p>
        </td>

        <td <?php echo $this->getAttrs(array('colspan' => 1)) .
            $this->getStyles(array('padding' => '8px 0 8px 8px'), true); ?>>
            <strong><?php echo $this->fontColor($order->getTotalForItems()->html($this->_getCurrency()), '#dd0055', 4); ?></strong>
        </td>
    </tr>
<?php endif; ?>
