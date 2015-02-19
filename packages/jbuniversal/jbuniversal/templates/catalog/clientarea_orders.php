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

$this->app->jbdebug->mark('template::orders::start');
$this->app->jblayout->setView($this);
$this->app->jbwrapper->start();

echo '<h1 class="title">' . JText::_('JBZOO_CLIENTAREA_ORDERS_TITLE') . '</h1>';

// items
if (count($this->orders) > 0) {
    echo $this->app->jblayout->render('clientarea_orders', $this->orders);
} else {
    echo JText::_('JBZOO_CLIENTAREA_ORDERS_EMPTY');
}

$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::orders::finish');
