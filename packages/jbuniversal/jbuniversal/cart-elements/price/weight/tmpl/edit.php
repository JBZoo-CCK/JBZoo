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

$jbHTML = $this->app->jbhtml;
$weight = array(
    'placeholder' => JText::_('JBZOO_PRICE_WEIGHT_WEIGHT')
);

echo $jbHTML->text($this->getControlName(), $value->get('value'), $jbHTML->buildAttrs($weight));