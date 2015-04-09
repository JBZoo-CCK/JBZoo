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

echo $this->_jbhtml->text($this->getControlName('value'), $value, $this->_jbhtml->buildAttrs(array(
    'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_WEIGHT_EDIT_PLACEHOLDER')
)));
