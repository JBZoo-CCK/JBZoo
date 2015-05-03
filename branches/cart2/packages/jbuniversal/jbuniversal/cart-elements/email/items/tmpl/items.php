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
        'cellspacing' => 0,
        'cellpadding' => 8,
        'width'       => '100%'
    )) .
    $this->getStyles(array(
        'border-collapse' => 'collapse'
    )); ?>
    >

    <?php echo $this->_partial('table_head'); ?>

    <?php echo $this->_partial('table_body'); ?>

    <tfoot>
    <?php echo $this->_partial('table_foot_subtotal'); ?>
    <?php echo $this->_partial('table_foot_modifiers'); ?>
    <?php echo $this->_partial('table_foot_payment'); ?>
    <?php echo $this->_partial('table_foot_shipping'); ?>
    <?php echo $this->_partial('table_foot_total'); ?>
    </tfoot>

</table>
