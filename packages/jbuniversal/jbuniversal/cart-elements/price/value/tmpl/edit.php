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

$elId = $this->app->jbstring->getId('value-');

echo $this->app->html->_('control.text', $this->getControlName('value'), $this->getValue()->data(true), array(
    'size'        => '10',
    'maxlength'   => '255',
    'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_VALUE'),
    'id'          => $elId . '-basic-value',
    'class'       => 'basic-value'
));
