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

if ($subtotal && $on) :
    if (!empty($orderModifiers)) :

        $count = count($orderModifiers);
        $i     = 0;
        foreach ($orderModifiers as $modifier) :
            $i++;

            $name = $modifier->getName();
            $rate = $modifier->getRate();

            if ($i == 1) : ?>

                <tr>
                    <td rowspan="<?php echo $count; ?>" colspan="2"
                        style="border-bottom: none;"></td>
                    <td rowspan="<?php echo $count; ?>" <?php echo $this->getStyles(); ?>>
                        <strong>Прочее</strong>
                        <br>
                        <i>(Элементы модификаторов цены)</i>
                    </td>

                    <td <?php echo $this->getStyles(); ?> colspan="2">
                        <?php echo $name; ?>
                    </td>
                    <td <?php echo $this->getStyles(array(
                            'text-align'    => 'right',
                            'border-bottom' => '1px solid #dddddd'
                        )
                    ); ?>
                        >
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
                        )
                    ); ?>
                        >
                        <?php echo $rate; ?>
                    </td>
                </tr>
            <?php
            endif;
        endforeach;
    endif;
endif;