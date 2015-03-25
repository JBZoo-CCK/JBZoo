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

$_height = array(
    'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_HEIGHT_UNIT')
);
$_length = array(
    'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_LENGTH_UNIT')
);
$_width  = array(
    'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_WIDTH_UNIT')
); ?>

<div class="jbprice-properties">
    <?php echo
    $this->_jbhtml->text($this->getControlName('height'), $height, $this->_jbhtml->buildAttrs($_height)),
    $this->_jbhtml->text($this->getControlName('length'), $length, $this->_jbhtml->buildAttrs($_length)),
    $this->_jbhtml->text($this->getControlName('width'), $width, $this->_jbhtml->buildAttrs($_width)); ?>
</div>