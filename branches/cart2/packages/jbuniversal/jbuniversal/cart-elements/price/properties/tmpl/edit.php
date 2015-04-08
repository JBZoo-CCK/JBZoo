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

<div class="jbprice-properties">
    <?php

    echo $this->_jbhtml->text($this->getControlName('height'), $height, array(
        'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_EDIT_PLACEHOLDER_HEIGHT')
    ));

    echo $this->_jbhtml->text($this->getControlName('length'), $legth, array(
        'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_EDIT_PLACEHOLDER_LENGTH')
    ));

    echo $this->_jbhtml->text($this->getControlName('width'), $width, array(
        'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_EDIT_PLACEHOLDER_WIDTH')
    ));

    ?>
</div>
