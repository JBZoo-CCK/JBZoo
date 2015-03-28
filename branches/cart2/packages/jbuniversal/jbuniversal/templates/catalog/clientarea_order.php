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

echo '<h1 class="title">' . $this->order->getName('full') . '</h1>';

echo $this->app->jblayout->render('clientarea_order', $this->order);

$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::order::finish');
