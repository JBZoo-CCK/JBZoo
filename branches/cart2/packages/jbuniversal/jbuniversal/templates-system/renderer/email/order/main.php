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
        'width'       => '100%',
        'bgcolor'     => '#fafafa',
        'cellspacing' => 0,
        'cellpadding' => 10
    )) .
    $this->getStyles(array(
        'border-collapse' => 'collapse',
        'border'          => '1px solid #dddddd',
        'border-radius'   => '4px'
    )); ?>
    >
    <tr>
        <td <?php echo $this->getAttrs(array(
            'colspan' => 2
        )); ?>
            >
            <h3 <?php echo $this->getStyles(array(
                'color'     => '#444444',
                'margin'    => '0 0 15px 0',
                'font-size' => '18px'
            )); ?> >
                <?php echo JText::_('JBZOO_ORDER_MAIN_TITLE'); ?>
            </h3>
        </td>
    </tr>
    <?php echo $this->renderPosition('main', array('style' => 'table')); ?>
</table>
