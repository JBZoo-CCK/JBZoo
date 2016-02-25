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
