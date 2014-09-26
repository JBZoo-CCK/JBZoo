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
<table <?php echo $this->getAttrs(array(
        'cellpadding' => 0,
        'width'       => '100%'
    )) .
    $this->getStyles(array(
        'border-collapse' => 'collapse',
        'font-size'       => '14px'
    ));?>
    >
    <?php if ($this->checkPosition('title')) : ?>
        <tr>
            <td>
                <h1>
                    <?php echo $this->renderPosition('title'); ?>
                </h1>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td <?php echo $this->getAttrs(array(
                'width'  => '65%',
                'height' => '50%',
                'valign' => 'top'
            )) .
            $this->getStyles(array(
                'padding' => '0 40px 0 0'
            )); ?>
            >
            <?php if ($this->checkPosition('items')) :
                echo $this->renderPosition('items');
            endif; ?>
        </td>
        <td <?php echo $this->getAttrs(array(
            'width'   => '33%',
            'rowspan' => '2'
        )); ?>>

            <?php if ($this->checkPosition('main')) :
                echo $this->partial('main');
            endif;

            if ($this->checkPosition('payment')) :
                echo $this->renderPosition('payment');
            endif;

            if ($this->checkPosition('shipping')) :
                echo $this->renderPosition('shipping');
            endif;

            if ($this->checkPosition('shippingfield')) :
                echo $this->renderPosition('shippingfield');
            endif; ?>

        </td>
    </tr>

    <tr>
        <td <?php echo $this->getAttrs(array(
            'width'  => '65%',
            'height' => '50%',
            'valign' => 'middle'
        )); ?>>
            <?php if ($this->checkPosition('info')) :
                echo $this->renderPosition('info');
            endif; ?>
        </td>
    </tr>

</table>

<table>
    <?php if ($this->checkPosition('advertise')) :
        echo $this->renderPosition('advertise', array('style' => 'table'));
    endif; ?>
</table>




