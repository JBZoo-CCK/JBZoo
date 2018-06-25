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

