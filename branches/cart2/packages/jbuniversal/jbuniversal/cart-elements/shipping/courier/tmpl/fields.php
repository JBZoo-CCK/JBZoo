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

$name       = $this->getControlName('delivery_date');
$idCalendar = $this->app->jbstring->getId('delivery-date');

echo $this->app->html->_('zoo.calendar', '', $name,
    $idCalendar, 'placeholder="' . JText::_('JBZOO_SHIPPING_COURIER_TIME_DELIVERY') . '"', true);
