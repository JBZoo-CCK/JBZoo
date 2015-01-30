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

if (!empty($modifiers)) :
    foreach ($modifiers as $key => $modifier) {
        $rate = $modifier->getRate();
        if ($rate->isEmpty()) {
            unset($modifiers[$key], $rate);
        }
    }

    $count = count($modifiers);
    $i     = 0;
    foreach ($modifiers as $modifier) :
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
                    <strong>Прочее</strong>
                </td>
                <td <?php echo $this->getStyles(); ?> colspan="2">
                    <?php echo $name; ?>
                </td>
                <td <?php echo $this->getStyles(array(
                    'text-align'    => 'right',
                    'border-bottom' => '1px solid #dddddd'
                )); ?>>
                    <?php echo $rate; ?>
                </td>
            </tr>
        <?php else : ?>
            <tr>
                <td <?php echo $this->getStyles(); ?> colspan="2">
                    <?php echo $name; ?>
                </td>
                <td <?php echo $this->getStyles(array(
                    'text-align'    => 'right',
                    'border-bottom' => '1px solid #dddddd'
                )); ?>>
                    <?php echo $rate; ?>
                </td>
            </tr>
        <?php endif;
    endforeach;
endif;
