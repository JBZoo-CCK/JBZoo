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

$modifiers = $order->getModifiersOrderPrice();
$user      = $order->getUser();
if (!empty($modifiers)) {
    $i = 0;
    foreach ($modifiers as $modifier) {
        if ($modifier->canAccess($user)) {
            $i++;
            $this->sum->add($modifier->get('rate'));

            if ($i == 1) { ?>
                <tr>
                    <td rowspan="<?php echo count($modifiers) - 1; ?>" class="noborder-btm"></td>
                    <td rowspan="<?php echo count($modifiers) - 1; ?>">
                        <strong><?php echo JText::_('JBZOO_ORDER_MODIFIERS_OTHER'); ?></strong><br>
                        <i>(<?php echo JText::_('JBZOO_ORDER_MODIFIERS_OTHER_ELEMENTS'); ?>)</i>
                    </td>
                    <td><?php echo $modifier->getName(); ?></td>
                    <td class="align-right"><?php echo $modifier->edit(); ?></td>
                    <td class="align-right"><?php echo $this->sum->html(); ?></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td><?php echo $modifier->getName(); ?></td>
                    <td class="align-right"><?php echo $modifier->edit(); ?></td>
                    <td class="align-right"><?php echo $this->sum->html(); ?></td>
                </tr>
                <?php
            }
            $i++;
        }
    }
}
