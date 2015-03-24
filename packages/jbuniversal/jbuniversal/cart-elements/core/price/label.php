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

// set attributes
$attributes = array('type' => 'text', 'name' => "{$control_name}[{$name}]", 'value' => JText::_($value), 'class' => isset($class) ? $class : '');

printf('<input %s />', $this->app->field->attributes($attributes, array('label', 'description')));