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
?>
<table <?php echo $this->getAttrs(array(
        'cellpadding' => 0,
        'width'       => '800px;'
    )) .
    $this->getStyles(array(
        'border-collapse' => 'collapse',
        'font-size'       => '14px'
    )); ?>
    >

    <?php if ($this->checkPosition('title')) : ?>
        <tr>
            <td><h1><?php echo $this->renderPosition('title'); ?></h1></td>
        </tr>
    <?php endif; ?>

    <tr>
        <td <?php echo $this->getAttrs(array(
                'valign' => 'top'
            )) .
            $this->getStyles(array(
                'padding' => '0 40px 0 0'
            )); ?>
            >
            <?php echo $this->renderPosition('items'); ?>
        </td>
    </tr>

    <tr>
        <td <?php echo $this->getAttrs(array(
            'valign' => 'top'
        )); ?>>
            <?php if ($this->checkPosition('info')) :
                echo $this->renderPosition('info');
            endif; ?>
        </td>
    </tr>

    <tr>
        <td <?php echo $this->getAttrs(array(
            'valign' => 'top'
        )); ?>>
            <?php if ($this->checkPosition('main')) {
                echo $this->partial('main');
            } ?>

            <?php echo $this->renderPosition('payment'); ?>
            <?php echo $this->renderPosition('shipping'); ?>
            <?php echo $this->renderPosition('shippingfield'); ?>
        </td>
    </tr>

</table>

<?php if ($this->checkPosition('advertise')) : ?>
    <table>
        <?php echo $this->renderPosition('advertise', array('style' => 'table')); ?>
    </table>
<?php endif; ?>
