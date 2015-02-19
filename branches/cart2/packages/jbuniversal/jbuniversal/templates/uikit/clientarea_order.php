<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->jbdoc->noindex();

$this->app->jbdebug->mark('template::order::start');
$this->app->jblayout->setView($this);
$this->app->jbwrapper->start();
$created = $this->app->html->_('date', $this->order->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());

echo '<h1 class="title"><h1>Заказ #' . $this->order->id . ' ' . JText::_('JBZOO_BY') . ' ' . $created . '</h1>';

echo $this->app->jblayout->render('clientarea_order', $this->order);

$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::order::finish');
