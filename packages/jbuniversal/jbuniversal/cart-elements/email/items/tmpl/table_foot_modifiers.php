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

if (!$this->config->get('modifiers', 1)) {
    return;
}

$modifiers = $order->getModifiersOrderPrice();

if (!empty($modifiers)) {

    foreach ($modifiers as $key => $modifier) {
        $rate = $modifier->getRate();
        if ($rate->isEmpty()) {
            unset($modifiers[$key], $rate);
        }
    }

    $count = count($modifiers);
    $i     = 0;
    foreach ($modifiers as $modifier) {
        $name = $modifier->getName();
        $rate = $modifier->getRate();
        if ($rate->isEmpty()) {
            continue;
        }

        $i++;

        if ($i == 1) : ?>
            <tr>
                <td rowspan="<?php echo $count; ?>" colspan="2" style="border-bottom: none;"></td>
                <td rowspan="<?php echo $count; ?>" <?php echo $this->getStyles(); ?>>
                    <strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_OTHER'); ?></strong>
                </td>
                <td <?php echo $this->getStyles(); ?> colspan="2">
                    <?php echo $name; ?>
                </td>
                <td <?php echo $this->getStyles(array('border-bottom' => '1px solid #dddddd')); ?>>
                    <?php echo $rate->html($this->_getCurrency()); ?>
                </td>
            </tr>
        <?php else : ?>
            <tr>
                <td <?php echo $this->getStyles(); ?> colspan="2">
                    <?php echo $name; ?>
                </td>
                <td <?php echo $this->getStyles(array('border-bottom' => '1px solid #dddddd')); ?>>
                    <?php echo $rate->html($this->_getCurrency()); ?>
                </td>
            </tr>
        <?php endif;
    }
}
